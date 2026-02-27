<?php

declare(strict_types=1);

namespace App\Statistic\Transport\Controller\Api\V1\Statistic;

use App\General\Transport\Rest\ResponseHandler;
use App\Statistic\Application\Service\StatisticService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for StatisticController endpoints.
 *
 * @package App\Statistic\Transport\Controller\Api\V1\Statistic
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/v1/statistics')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Statistics')]
class StatisticController
{
    public function __construct(
        private readonly ResponseHandler $responseHandler,
        private readonly StatisticService $statisticService,
    ) {
    }

    #[Route(path: '/overview', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'Dashboard overview cards')]
    public function overview(Request $request): Response
    {
        return $this->responseHandler->createResponse($request, $this->statisticService->getOverview());
    }

    #[Route(path: '/entities', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'Counts by entity')]
    public function entities(Request $request): Response
    {
        return $this->responseHandler->createResponse($request, $this->statisticService->getEntityCounters());
    }

    #[Route(path: '/timeseries', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'Global created entities grouped by day')]
    public function timeSeries(Request $request): Response
    {
        $days = (int) $request->query->get('days', 30);

        return $this->responseHandler->createResponse($request, $this->statisticService->getTimeSeries($days));
    }

    #[Route(path: '/timeseries/{entity}', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'Created entities grouped by day for one entity')]
    public function timeSeriesByEntity(Request $request, string $entity): Response
    {
        $days = (int) $request->query->get('days', 30);

        try {
            $data = $this->statisticService->getTimeSeriesByEntity($entity, $days);
        } catch (\RuntimeException $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }

        return $this->responseHandler->createResponse($request, $data);
    }

    #[Route(path: '/distributions/statuses', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'Status distributions for chart widgets')]
    public function statusDistributions(Request $request): Response
    {
        return $this->responseHandler->createResponse($request, $this->statisticService->getStatusDistributions());
    }
}
