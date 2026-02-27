<?php

declare(strict_types=1);

namespace App\General\Application\Service\Rest;

use App\General\Application\DTO\Rest\ResourceSchemaMetadataDto;
use App\General\Application\DTO\Rest\ResourceSchemaRelationDto;
use App\General\Application\Rest\Interfaces\BaseRestResourceInterface;
use App\General\Transport\AutoMapper\RestRequestMapper;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;
use Symfony\Component\Serializer\Attribute\Groups;

use function array_keys;
use function array_unique;
use function class_exists;
use function explode;
use function is_array;
use function is_int;
use function is_string;
use function ksort;
use function sort;
use function str_contains;
use function str_ends_with;
use function strrpos;
use function substr;

final class ResourceSchemaService
{
    /**
     * @param array<int, class-string> $dtoClasses
     */
    public function build(BaseRestResourceInterface $resource, array $dtoClasses): ResourceSchemaMetadataDto
    {
        return new ResourceSchemaMetadataDto(
            $this->extractDisplayableProperties($resource),
            $this->extractEditableProperties($dtoClasses),
            $this->extractRelations($resource->getRepository()->getAssociations()),
        );
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
     * @param array<string, array<string, mixed>> $associations
     *
     * @return array<string, ResourceSchemaRelationDto>
     */
    private function extractRelations(array $associations): array
    {
        $relations = [];

        foreach ($associations as $association => $mapping) {
            $relations[$association] = new ResourceSchemaRelationDto(
                is_string($mapping['targetEntity'] ?? null) ? $mapping['targetEntity'] : '',
                $this->normalizeRelationType(is_int($mapping['type'] ?? null) ? $mapping['type'] : null),
            );
        }

        ksort($relations);

        return $relations;
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

    private function normalizeRelationType(?int $relationType): string
    {
        return match ($relationType) {
            ClassMetadata::ONE_TO_ONE => 'oneToOne',
            ClassMetadata::MANY_TO_ONE => 'manyToOne',
            ClassMetadata::ONE_TO_MANY => 'oneToMany',
            ClassMetadata::MANY_TO_MANY => 'manyToMany',
            default => 'unknown',
        };
    }
}
