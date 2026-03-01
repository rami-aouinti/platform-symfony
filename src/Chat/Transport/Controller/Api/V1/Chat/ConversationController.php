<?php

declare(strict_types=1);

namespace App\Chat\Transport\Controller\Api\V1\Chat;

use App\Chat\Application\Resource\Interfaces\ChatResourceInterface;
use App\General\Transport\Rest\ResponseHandler;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * API controller for ConversationController endpoints.
 *
 * @package App\Chat\Transport\Controller\Api\V1\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/api/v1/me/chat/conversations')]
#[Route(path: '/v1/chat/conversations')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Chat')]
class ConversationController extends AbstractController
{
    public function __construct(
        private readonly ChatResourceInterface $resource,
        private readonly ResponseHandler $responseHandler,
    ) {
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'List current user conversations')]
    #[OA\Response(response: 200, description: 'Conversation list', content: new JsonContent(type: 'array', items: new OA\Items(type: 'object')))]
    public function listAction(Request $request): Response
    {
        return $this->responseHandler->createResponse(
            $request,
            $this->resource->listConversationsForCurrentUser(),
            $this->resource,
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
        return $this->responseHandler->createResponse(
            $request,
            $this->resource->getConversation($id),
            $this->resource,
        );
    }
}
