<?php

declare(strict_types=1);

namespace App\General\Application\Service\Rest;

use App\General\Application\Rest\Interfaces\BaseRestResourceInterface;
use App\General\Transport\AutoMapper\RestRequestMapper;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;
use Symfony\Component\Serializer\Attribute\Groups;

use function array_keys;
use function array_unique;
use function class_exists;
use function explode;
use function in_array;
use function is_array;
use function is_bool;
use function is_string;
use function preg_replace;
use function sort;
use function str_contains;
use function str_ends_with;
use function str_replace;
use function strrpos;
use function strtolower;
use function substr;

final class ResourceSchemaService
{
    /**
     * @param array<int, class-string> $dtoClasses
     */
    public function build(
        BaseRestResourceInterface $resource,
        array $dtoClasses,
        ?string $createDtoClass = null,
        array $configuration = []
    ): array {
        $metadata = $resource->getRepository()->getClassMetaData();

        $displayable = $this->hydrateFields($this->extractDisplayableProperties($resource), $metadata);
        $editable = $this->hydrateFields($this->extractEditableProperties($dtoClasses), $metadata);
        $creatable = $this->hydrateFields(
            $createDtoClass !== null ? $this->extractEditableProperties([$createDtoClass]) : [],
            $metadata,
        );

        return [
            'displayable' => $this->applySectionConfiguration($displayable, $configuration['displayable'] ?? []),
            'editable' => $this->applySectionConfiguration($editable, $configuration['editable'] ?? []),
            'creatable' => $this->resolveCreatableConfiguration(
                $creatable,
                $configuration['creatable'] ?? [
                    'fields' => $creatable,
                    'required' => [],
                ],
                $metadata,
            ),
        ];
    }

    /**
     * @param array<int, class-string> $dtoClasses
     *
     * @return array<int, string>
     */
    private function extractEditableProperties(array $dtoClasses): array
    {
        $editable = [];

        foreach ($dtoClasses as $dtoClass) {
            $mapperClass = $this->resolveMapperClass($dtoClass);

            if ($mapperClass === null || !is_subclass_of($mapperClass, RestRequestMapper::class)) {
                continue;
            }

            $editable = [...$editable, ...$mapperClass::getEditableProperties()];
        }

        $editable = array_values(array_unique($editable));
        sort($editable);

        return $editable;
    }

    /**
     * @return array<int, string>
     */
    private function extractDisplayableProperties(BaseRestResourceInterface $resource): array
    {
        $displayable = $this->extractDisplayableFromSerializerGroups($resource->getEntityName());

        if ($displayable === []) {
            $metadata = $resource->getRepository()->getClassMetaData();
            $displayable = [...array_keys($metadata->fieldMappings), ...array_keys($metadata->associationMappings)];
        }

        $displayable = array_values(array_unique($displayable));
        sort($displayable);

        return $displayable;
    }

    /**
     * @param class-string $entityClass
     *
     * @return array<int, string>
     */
    private function extractDisplayableFromSerializerGroups(string $entityClass): array
    {
        if (!class_exists($entityClass)) {
            return [];
        }

        $reflectionClass = new ReflectionClass($entityClass);
        $displayable = [];

        foreach ($reflectionClass->getProperties() as $property) {
            foreach ($property->getAttributes(Groups::class) as $attribute) {
                $arguments = $attribute->getArguments();
                $groups = $arguments[0] ?? [];

                if (!is_array($groups)) {
                    continue;
                }

                foreach ($groups as $group) {
                    if (is_string($group) && str_ends_with($group, '.show')) {
                        $displayable[] = $property->getName();
                        break;
                    }
                }
            }
        }

        if ($reflectionClass->hasProperty('id')) {
            $displayable[] = 'id';
        }

        return $displayable;
    }

    /**
     * @param array<int, string> $properties
     *
     * @return array<int, array<string, string|null>>
     */
    private function hydrateFields(array $properties, ClassMetadata $metadata): array
    {
        $hydrated = [];

        foreach ($properties as $property) {
            $field = $this->buildFieldFromName($property, $metadata);

            if ($field !== null) {
                $hydrated[] = $field;
            }
        }

        return $hydrated;
    }

    /**
     * @param array<int, array<string, string|null>> $autoFields
     * @param array<int, string|array<string, string|null>>|bool $configuration
     *
     * @return array<int, array<string, string|null>>|false
     */
    private function applySectionConfiguration(array $autoFields, array|bool $configuration): array|false
    {
        if (is_bool($configuration)) {
            return $configuration === false ? false : $autoFields;
        }

        if ($configuration === []) {
            return $autoFields;
        }

        $resolved = [];

        foreach ($configuration as $configuredField) {
            $normalized = $this->normalizeConfiguredField($configuredField, $autoFields);

            if ($normalized !== null) {
                $resolved[] = $normalized;
            }
        }

        return $resolved;
    }

    /**
     * @param array<int, array<string, string|null>> $autoFields
     * @param array{fields?: array<int, string|array<string, string|null>>, required?: array<int, string>}|bool $configuration
     *
     * @return array<string, mixed>|false
     */
    private function resolveCreatableConfiguration(array $autoFields, array|bool $configuration, ClassMetadata $metadata): array|false
    {
        if ($configuration === false) {
            return false;
        }

        $fieldsConfig = is_array($configuration) ? ($configuration['fields'] ?? []) : [];
        $required = is_array($configuration) && is_array($configuration['required'] ?? null)
            ? array_values(array_unique($configuration['required']))
            : [];

        return [
            'fields' => $this->applySectionConfiguration($autoFields, $fieldsConfig),
            'required' => $this->filterRequiredFields($required, $metadata),
        ];
    }

    /**
     * @param array<int, string> $required
     *
     * @return array<int, string>
     */
    private function filterRequiredFields(array $required, ClassMetadata $metadata): array
    {
        $filtered = [];

        foreach ($required as $fieldName) {
            if (!is_string($fieldName)) {
                continue;
            }

            if (isset($metadata->fieldMappings[$fieldName]) || isset($metadata->associationMappings[$fieldName])) {
                $filtered[] = $fieldName;
            }
        }

        return array_values(array_unique($filtered));
    }

    /**
     * @param string|array<string, string|null> $configuredField
     * @param array<int, array<string, string|null>> $autoFields
     *
     * @return array<string, string|null>|null
     */
    private function normalizeConfiguredField(string|array $configuredField, array $autoFields): ?array
    {
        $autoByName = [];

        foreach ($autoFields as $field) {
            $name = $field['name'] ?? null;

            if (is_string($name)) {
                $autoByName[$name] = $field;
            }
        }

        if (is_string($configuredField)) {
            return $autoByName[$configuredField] ?? null;
        }

        $name = $configuredField['name'] ?? null;

        if (!is_string($name) || $name === '') {
            return null;
        }

        $base = $autoByName[$name] ?? null;

        if ($base === null) {
            return null;
        }

        foreach (['type', 'targetClass', 'endpoint'] as $key) {
            if (isset($configuredField[$key])) {
                $base[$key] = $configuredField[$key];
            }
        }

        return $base;
    }

    /**
     * @return array<string, string|null>|null
     */
    private function buildFieldFromName(string $name, ClassMetadata $metadata): ?array
    {
        if (isset($metadata->associationMappings[$name])) {
            $targetClass = is_string($metadata->associationMappings[$name]['targetEntity'] ?? null)
                ? $metadata->associationMappings[$name]['targetEntity']
                : null;

            return [
                'name' => $name,
                'type' => 'object',
                'targetClass' => $targetClass,
                'endpoint' => $targetClass !== null ? $this->guessEndpointFromEntity($targetClass) : null,
            ];
        }

        if (isset($metadata->fieldMappings[$name])) {
            $doctrineType = is_string($metadata->fieldMappings[$name]['type'] ?? null)
                ? $metadata->fieldMappings[$name]['type']
                : null;

            return [
                'name' => $name,
                'type' => $this->normalizeFieldType($doctrineType),
                'targetClass' => null,
                'endpoint' => null,
            ];
        }

        return null;
    }

    /**
     * @param class-string $dtoClass
     *
     * @return class-string<RestRequestMapper>|null
     */
    private function resolveMapperClass(string $dtoClass): ?string
    {
        if (!str_contains($dtoClass, '\\Application\\DTO\\')) {
            return null;
        }

        [$moduleNamespace, $dtoNamespace] = explode('\\Application\\DTO\\', $dtoClass, 2);

        $position = strrpos($dtoNamespace, '\\');

        if ($position === false) {
            return null;
        }

        $resourceNamespace = substr($dtoNamespace, 0, $position);
        $mapperClass = $moduleNamespace . '\\Transport\\AutoMapper\\' . $resourceNamespace . '\\RequestMapper';

        return class_exists($mapperClass) ? $mapperClass : null;
    }

    private function normalizeFieldType(?string $doctrineType): string
    {
        if ($doctrineType === null) {
            return 'normal';
        }

        return in_array($doctrineType, [Types::BOOLEAN], true) ? 'boolean' : 'normal';
    }

    /**
     * @param class-string $entityClass
     */
    private function guessEndpointFromEntity(string $entityClass): string
    {
        $shortName = substr($entityClass, (int) strrpos($entityClass, '\\') + 1);
        $snakeCase = (string) preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName);
        $resource = strtolower(str_replace('__', '_', $snakeCase));

        return '/api/v1/' . $resource . 's';
    }
}
