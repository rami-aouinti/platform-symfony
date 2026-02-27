<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use Doctrine\ORM\Mapping\ClassMetadata;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function array_keys;
use function array_unique;
use function count;
use function is_int;
use function is_string;
use function ksort;
use function lcfirst;
use function sort;
use function str_starts_with;
use function strlen;
use function substr;

/**
 * @package App\General\Transport\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait SchemaMethod
{
    /**
     * Generic 'schemaMethod' method for REST resources.
     *
     * @param array<int, string>|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function schemaMethod(Request $request, ?array $allowedHttpMethods = null): Response
    {
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_GET]);

        $data = $this->rememberReadEndpoint(
            $request,
            ['type' => 'schema'],
            function () use ($resource): array {
                $schema = $this->extractSchemaData();
                $schema['relations'] = $this->extractRelations($resource->getRepository()->getAssociations());

                return $schema;
            },
        );

        return $this->getResponseHandler()->createResponse($request, $data, $resource); /** @phpstan-ignore-line */
    }

    /**
     * @return array{displayable: array<int, string>, editable: array<int, string>}
     */
    private function extractSchemaData(): array
    {
        $displayable = [];
        $editable = [];

        foreach ($this->resolveSchemaDtoClasses() as $dtoClass) {
            [$dtoDisplayable, $dtoEditable] = $this->extractDtoProperties($dtoClass);
            $displayable = [...$displayable, ...$dtoDisplayable];
            $editable = [...$editable, ...$dtoEditable];
        }

        $displayable = array_values(array_unique($displayable));
        $editable = array_values(array_unique($editable));

        sort($displayable);
        sort($editable);

        return [
            'displayable' => $displayable,
            'editable' => $editable,
        ];
    }

    /**
     * @return array<int, class-string>
     */
    private function resolveSchemaDtoClasses(): array
    {
        $dtoClasses = [$this->getDtoClass()];

        foreach ([Controller::METHOD_CREATE, Controller::METHOD_UPDATE, Controller::METHOD_PATCH] as $method) {
            try {
                $dtoClasses[] = $this->getDtoClass($method);
            } catch (Throwable) {
                // Some controllers can disable these methods, so we simply skip unsupported DTOs.
            }
        }

        /** @var array<int, class-string> $dtoClasses */
        return array_values(array_unique($dtoClasses));
    }

    /**
     * @param class-string $dtoClass
     *
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function extractDtoProperties(string $dtoClass): array
    {
        try {
            $reflection = new ReflectionClass($dtoClass);
        } catch (ReflectionException) {
            return [[], []];
        }

        $displayable = [];
        $editable = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $methodName = $method->getName();

            if (str_starts_with($methodName, 'set') && strlen($methodName) > 3 && count($method->getParameters()) === 1) {
                $editable[] = lcfirst(substr($methodName, 3));

                continue;
            }

            if (
                str_starts_with($methodName, 'get')
                && strlen($methodName) > 3
                && count($method->getRequiredParameters()) === 0
            ) {
                $displayable[] = lcfirst(substr($methodName, 3));

                continue;
            }

            if (
                (str_starts_with($methodName, 'is') || str_starts_with($methodName, 'has'))
                && strlen($methodName) > 2
                && count($method->getRequiredParameters()) === 0
            ) {
                $displayable[] = lcfirst(substr($methodName, 2));
            }
        }

        return [
            array_values(array_unique($displayable)),
            array_values(array_unique($editable)),
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $associations
     *
     * @return array<string, array{type: string, targetEntity: string}>
     */
    private function extractRelations(array $associations): array
    {
        $relations = [];

        foreach (array_keys($associations) as $association) {
            $mapping = $associations[$association];
            $relationType = $mapping['type'] ?? null;
            $targetEntity = $mapping['targetEntity'] ?? null;

            $relations[$association] = [
                'type' => $this->normalizeRelationType(is_int($relationType) ? $relationType : null),
                'targetEntity' => is_string($targetEntity) ? $targetEntity : '',
            ];
        }

        ksort($relations);

        return $relations;
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
