<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use App\General\Application\Service\Rest\ResourceSchemaService;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use Throwable;
use UnexpectedValueException;

use function array_unique;
use function array_values;

/**
 * @package App\General\Transport\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 */
trait SchemaMethod
{
    private ?ResourceSchemaService $resourceSchemaService = null;

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
            fn () => $this->getResourceSchemaService()->build(
                $resource,
                $this->resolveSchemaDtoClasses(),
                $this->getSchemaFieldConfiguration(),
            ),
        );

        return $this->getResponseHandler()->createResponse($request, $data, $resource); /** @phpstan-ignore-line */
    }

    #[Required]
    public function setResourceSchemaService(ResourceSchemaService $resourceSchemaService): static
    {
        $this->resourceSchemaService = $resourceSchemaService;

        return $this;
    }

    /**
     * @return array<int, class-string>
     */
    private function resolveSchemaDtoClasses(): array
    {
        $dtoClasses = [];

        try {
            $dtoClasses[] = $this->getDtoClass();
        } catch (Throwable) {
            // Some resources don't have a default DTO class (read-only/list/count split controllers).
        }

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
     * Override this in specific controllers when you need manual schema control.
     *
     * Supported format:
     *  [
     *      'displayable' => [
     *          'title',
     *          ['name' => 'project', 'type' => 'object', 'endpoint' => '/api/v1/projects'],
     *      ],
     *      'editable' => ['title'],
     *  ]
     *
     * @return array<string, array<int, string|array<string, string|null>>>
     */
    protected function getSchemaFieldConfiguration(): array
    {
        return [];
    }

    private function getResourceSchemaService(): ResourceSchemaService
    {
        return $this->resourceSchemaService
            ?? throw new UnexpectedValueException('ResourceSchemaService service not set');
    }
}
