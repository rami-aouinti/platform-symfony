<?php

declare(strict_types=1);

namespace App\Chat\Transport\Controller\Api\V1\Chat;

use App\Chat\Application\DTO\Chat\ChatMessageSend;
use App\Chat\Application\Resource\Interfaces\ChatResourceInterface;
use App\General\Domain\Utils\JSON;
use App\General\Transport\Rest\ResponseHandler;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
#[OA\Tag(name: 'Chat')]
class MessageController extends AbstractController
{
    public function __construct(
        private readonly ChatResourceInterface $resource,
        private readonly ResponseHandler $responseHandler,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route(path: '/api/v1/me/chat/conversations/{id}/messages', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_GET])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Get(summary: 'List conversation messages for current user')]
    public function listAction(Request $request, string $id): Response
    {
        return $this->responseHandler->createResponse($request, $this->resource->listMessages($id), $this->resource);
    }

    #[Route(path: '/api/v1/me/chat/conversations/{id}/messages', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_POST])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Post(summary: 'Create message in a conversation for current user')]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['content'],
        properties: [new OA\Property(property: 'content', type: 'string', maxLength: 10000)],
        type: 'object',
    ))]
    public function createAction(Request $request, string $id): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);

        $dto = (new ChatMessageSend())->setContent((string)($payload['content'] ?? ''));
        $violations = $this->validator->validate($dto);

        if ($violations->count() > 0) {
            return $this->responseHandler->getValidationErrorResponse($request, $violations);
        }

        return $this->responseHandler->createResponse($request, $this->resource->createMessage($id, $dto->getContent()), $this->resource);
    }

    #[Route(path: '/api/v1/me/chat/messages/{messageId}', requirements: ['messageId' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Patch(summary: 'Update current user message')]
    public function patchAction(Request $request, string $messageId): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);

        $dto = (new ChatMessageSend())->setContent((string)($payload['content'] ?? ''));
        $violations = $this->validator->validate($dto);

        if ($violations->count() > 0) {
            return $this->responseHandler->getValidationErrorResponse($request, $violations);
        }

        return $this->responseHandler->createResponse($request, $this->resource->updateMessage($messageId, $dto->getContent()), $this->resource);
    }

    #[Route(path: '/api/v1/me/chat/messages/{messageId}', requirements: ['messageId' => Requirement::UUID_V1], methods: [Request::METHOD_DELETE])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Delete(summary: 'Delete current user message')]
    public function deleteAction(Request $request, string $messageId): Response
    {
        $this->resource->deleteMessage($messageId);

        return $this->responseHandler->createResponse($request, ['deleted' => true], $this->resource);
    }

    #[Route(path: '/api/v1/admin/chat/messages', methods: [Request::METHOD_GET])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
    #[OA\Get(summary: 'List chat messages for moderation/audit')]
    public function adminListAction(Request $request): Response
    {
        return $this->responseHandler->createResponse($request, $this->resource->listMessagesForModeration(), $this->resource);
    }

    #[Route(path: '/api/v1/admin/chat/messages/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_DELETE])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
    #[OA\Delete(summary: 'Delete a message for moderation/audit')]
    public function adminDeleteAction(Request $request, string $id): Response
    {
        $this->resource->deleteMessageForModeration($id);

        return $this->responseHandler->createResponse($request, ['deleted' => true], $this->resource);
    }
}
