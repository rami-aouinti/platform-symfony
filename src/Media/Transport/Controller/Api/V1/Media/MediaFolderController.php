<?php

declare(strict_types=1);

namespace App\Media\Transport\Controller\Api\V1\Media;

use App\Media\Application\Resource\Interfaces\MediaFolderResourceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function is_array;
use function is_bool;
use function is_string;

#[AsController]
#[Route(path: '/v1/me/media/folders')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
class MediaFolderController extends AbstractController
{
    public function __construct(
        private readonly MediaFolderResourceInterface $mediaFolderResource,
    ) {
    }

    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function listAction(Request $request): JsonResponse
    {
        $parentId = $request->query->getString('parentId') ?: null;
        $tree = $request->query->getBoolean('tree', false);

        return $this->json($this->mediaFolderResource->list($parentId, $tree));
    }

    #[Route(path: '', methods: [Request::METHOD_POST])]
    public function createAction(Request $request): JsonResponse
    {
        $payload = $this->getPayload($request);
        $name = $payload['name'] ?? null;
        $parentId = $payload['parentId'] ?? null;

        if (!is_string($name)) {
            throw new BadRequestHttpException('Field "name" is required and must be a string.');
        }

        if ($parentId !== null && !is_string($parentId)) {
            throw new BadRequestHttpException('Field "parentId" must be a string when provided.');
        }

        $folder = $this->mediaFolderResource->create($name, $parentId);

        return $this->json($this->mediaFolderResource->toArray($folder), Response::HTTP_CREATED);
    }

    #[Route(path: '/{id}', methods: [Request::METHOD_PATCH])]
    public function patchAction(Request $request, string $id): JsonResponse
    {
        $payload = $this->getPayload($request);
        $name = $payload['name'] ?? null;

        if (!is_string($name)) {
            throw new BadRequestHttpException('Field "name" is required and must be a string.');
        }

        $folder = $this->mediaFolderResource->rename($id, $name);

        return $this->json($this->mediaFolderResource->toArray($folder));
    }

    #[Route(path: '/{id}', methods: [Request::METHOD_DELETE])]
    public function deleteAction(Request $request, string $id): JsonResponse
    {
        $cascadeQuery = $request->query->get('cascade');
        $cascade = is_bool($cascadeQuery) ? $cascadeQuery : $request->query->getBoolean('cascade', false);

        $this->mediaFolderResource->delete($id, $cascade);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /** @return array<string, mixed> */
    private function getPayload(Request $request): array
    {
        $payload = $request->toArray();

        if (!is_array($payload)) {
            throw new BadRequestHttpException('Invalid JSON payload.');
        }

        return $payload;
    }
}
