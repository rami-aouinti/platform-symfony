<?php

declare(strict_types=1);

namespace App\Chat\Transport\Controller\Api\V1\Chat;

use App\Chat\Application\DTO\Chat\ChatMessageSend;
use App\Chat\Application\Resource\Interfaces\ChatResourceInterface;
use App\General\Domain\Utils\JSON;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

/**
 * @method ChatResourceInterface getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/v1/chat/conversations')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Chat')]
class ConversationController extends Controller
{
    public function __construct(
        ChatResourceInterface $resource,
        private readonly ValidatorInterface $validator,
    ) {
        parent::__construct($resource);
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'List current user conversations')]
    #[OA\Response(response: 200, description: 'Conversation list', content: new JsonContent(type: 'array', items: new OA\Items(type: 'object')))]
    public function listAction(Request $request): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->listConversationsForCurrentUser(),
            $this->getResource(),
        );
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'Get conversation detail')]
    #[OA\Response(response: 200, description: 'Conversation detail', content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 403, description: 'Forbidden: current user is not a participant or conversation is not eligible for chat.')]
    #[OA\Response(response: 404, description: 'Conversation not found.')]
    public function detailAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->getConversation($id),
            $this->getResource(),
        );
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}/messages', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Send message in conversation')]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['content'],
        properties: [new OA\Property(property: 'content', type: 'string', maxLength: 10000, example: 'Bonjour, je confirme ma disponibilité cette semaine.')],
        type: 'object',
    ))]
    #[OA\Response(response: 200, description: 'Conversation with sent message', content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 403, description: 'Forbidden: current user cannot post in this conversation.')]
    #[OA\Response(response: 404, description: 'Conversation not found.')]
    public function sendMessageAction(Request $request, string $id): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);

        $dto = (new ChatMessageSend())->setContent((string)($payload['content'] ?? ''));
        $violations = $this->validator->validate($dto);

        if ($violations->count() > 0) {
            return $this->getResponseHandler()->getValidationErrorResponse($request, $violations);
        }

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->sendMessage($id, $dto->getContent()),
            $this->getResource(),
        );
    }
}
