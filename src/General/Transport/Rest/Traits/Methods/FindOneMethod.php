<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Methods;

use App\General\Application\Rest\Interfaces\RestFindOneResourceInterface;
use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\ResponseHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\General\Transport\Rest\Traits\Methods
 *
 * @method ResponseHandler getResponseHandler()
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait FindOneMethod
{
    /**
     * Generic 'findOneMethod' method for REST resources.
     *
     * @param array<int, string>|null $allowedHttpMethods
     *
     * @throws Throwable
     */
    public function findOneMethod(Request $request, string $id, ?array $allowedHttpMethods = null): Response
    {
        /** @var RestResourceInterface|RestFindOneResourceInterface $resource */
        $resource = $this->getResourceForMethod($request, $allowedHttpMethods ?? [Request::METHOD_GET]);

        try {
            $entityManagerName = RequestHandler::getTenant($request);

            $data = $this->rememberReadEndpoint(
                $request,
                [
                    'criteria' => ['id' => $id],
                    'tenant' => $entityManagerName,
                ],
                fn (): mixed => $resource->findOne($id, true, $entityManagerName),
            );

            return $this->getResponseHandler()->createResponse(
                $request,
                $data, /** @phpstan-ignore-next-line */
                $resource
            );
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException($exception, $id, $entityManagerName ?? null);
        }
    }
}
