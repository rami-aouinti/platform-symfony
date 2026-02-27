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
    public function build(BaseRestResourceInterface $resource, array $dtoClasses): array
    {
        $metadata = $resource->getRepository()->getClassMetaData();

        return [
            "displayable" => $this->hydrateFields($this->extractDisplayableProperties($resource), $metadata),
            "editable" => $this->hydrateFields($this->extractEditableProperties($dtoClasses), $metadata),
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
            if (isset($metadata->associationMappings[$property])) {
                $targetClass = is_string($metadata->associationMappings[$property]['targetEntity'] ?? null)
                    ? $metadata->associationMappings[$property]['targetEntity']
                    : null;

                $hydrated[] = [
                    'name' => $property,
                    'type' => 'object',
                    'targetClass' => $targetClass,
                    'endpoint' => $targetClass !== null ? $this->guessEndpointFromEntity($targetClass) : null,
                ];

                continue;
            }

            $doctrineType = is_string($metadata->fieldMappings[$property]['type'] ?? null)
                ? $metadata->fieldMappings[$property]['type']
                : null;

            $hydrated[] = [
                'name' => $property,
                'type' => $this->normalizeFieldType($doctrineType),
                'targetClass' => null,
                'endpoint' => null,
            ];
        }

        return $hydrated;
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
