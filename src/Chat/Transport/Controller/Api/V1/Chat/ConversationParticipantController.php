<?php

declare(strict_types=1);

namespace App\Chat\Transport\Controller\Api\V1\Chat;

use App\Chat\Application\Resource\Interfaces\ChatResourceInterface;
use App\General\Domain\Utils\JSON;
use App\General\Transport\Rest\ResponseHandler;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[OA\Tag(name: 'Chat')]
class ConversationParticipantController extends AbstractController
{
    public function __construct(
        private readonly ChatResourceInterface $resource,
        private readonly ResponseHandler $responseHandler,
    ) {
    }

    #[Route(path: '/api/v1/me/chat/conversations/{id}/participants', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_POST])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Post(summary: 'Add participant to a conversation for current user')]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['userId'],
        properties: [new OA\Property(property: 'userId', type: 'string', format: 'uuid')],
        type: 'object',
    ))]
    public function addAction(Request $request, string $id): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);
        $userId = (string)($payload['userId'] ?? '');

        if ($userId === '') {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Field "userId" is required.');
        }

        return $this->responseHandler->createResponse($request, $this->resource->addParticipant($id, $userId), $this->resource);
    }

    #[Route(path: '/api/v1/me/chat/conversations/{id}/participants/{userId}', requirements: ['id' => Requirement::UUID_V1, 'userId' => Requirement::UUID_V1], methods: [Request::METHOD_DELETE])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Delete(summary: 'Remove participant from a conversation for current user')]
    public function removeAction(Request $request, string $id, string $userId): Response
    {
        return $this->responseHandler->createResponse($request, $this->resource->removeParticipant($id, $userId), $this->resource);
    }
}
