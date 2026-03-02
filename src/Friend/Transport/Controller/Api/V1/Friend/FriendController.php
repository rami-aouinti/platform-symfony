<?php

declare(strict_types=1);

namespace App\Friend\Transport\Controller\Api\V1\Friend;

use App\Friend\Domain\Entity\FriendRequest;
use App\Friend\Infrastructure\Repository\FriendRequestRepository;
use App\General\Domain\Utils\JSON;
use App\User\Domain\Entity\User;
use App\User\Infrastructure\Repository\UserRepository;
use JsonException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: '/v1/me/friends')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Me/Friends')]
class FriendController
{
    public function __construct(
        private readonly FriendRequestRepository $friendRequestRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function listFriendsAction(User $loggedInUser): JsonResponse
    {
        $friendships = $this->friendRequestRepository->findAcceptedByUser($loggedInUser->getId());

        $friends = [];
        foreach ($friendships as $friendship) {
            $friend = $friendship->getRequester()->getId() === $loggedInUser->getId()
                ? $friendship->getAddressee()
                : $friendship->getRequester();

            $friends[] = $this->normalizeUser($friend);
        }

        return new JsonResponse(['items' => $friends]);
    }

    /** @throws JsonException */
    #[Route(path: '/requests', methods: [Request::METHOD_POST])]
    public function addFriendAction(Request $request, User $loggedInUser): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);
        $targetUserId = $payload['userId'] ?? null;

        if (!is_string($targetUserId) || $targetUserId === '') {
            return new JsonResponse(['message' => 'Field "userId" is required.'], Response::HTTP_BAD_REQUEST);
        }

        if ($targetUserId === $loggedInUser->getId()) {
            return new JsonResponse(['message' => 'You cannot send a friend request to yourself.'], Response::HTTP_BAD_REQUEST);
        }

        $targetUser = $this->userRepository->find($targetUserId);
        if (!$targetUser instanceof User) {
            return new JsonResponse(['message' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $existing = $this->friendRequestRepository->findBetweenUsers($loggedInUser->getId(), $targetUserId);
        if ($existing instanceof FriendRequest) {
            if ($existing->isAccepted()) {
                return new JsonResponse(['message' => 'Already friends.'], Response::HTTP_CONFLICT);
            }

            if ($existing->getRequester()->getId() === $targetUserId) {
                $existing->accept();
                $this->friendRequestRepository->save($existing, true);

                return new JsonResponse(['message' => 'Friend request accepted.']);
            }

            return new JsonResponse(['message' => 'Friend request already sent.'], Response::HTTP_CONFLICT);
        }

        $friendRequest = new FriendRequest($loggedInUser, $targetUser);
        $this->friendRequestRepository->save($friendRequest, true);

        return new JsonResponse(['id' => $friendRequest->getId(), 'message' => 'Friend request sent.'], Response::HTTP_CREATED);
    }

    #[Route(path: '/requests/{id}/accept', methods: [Request::METHOD_POST])]
    public function acceptFriendRequestAction(string $id, User $loggedInUser): JsonResponse
    {
        $friendRequest = $this->friendRequestRepository->find($id);
        if (!$friendRequest instanceof FriendRequest) {
            return new JsonResponse(['message' => 'Friend request not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($friendRequest->getAddressee()->getId() !== $loggedInUser->getId()) {
            return new JsonResponse(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN);
        }

        if ($friendRequest->isAccepted()) {
            return new JsonResponse(['message' => 'Already accepted.']);
        }

        $friendRequest->accept();
        $this->friendRequestRepository->save($friendRequest, true);

        return new JsonResponse(['message' => 'Friend request accepted.']);
    }

    #[Route(path: '/{userId}', methods: [Request::METHOD_DELETE])]
    public function removeFriendAction(string $userId, User $loggedInUser): JsonResponse
    {
        $friendship = $this->friendRequestRepository->findBetweenUsers($loggedInUser->getId(), $userId);

        if (!$friendship instanceof FriendRequest || !$friendship->isAccepted()) {
            return new JsonResponse(['message' => 'Friend not found.'], Response::HTTP_NOT_FOUND);
        }

        $this->friendRequestRepository->remove($friendship, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/requests/received', methods: [Request::METHOD_GET])]
    public function listReceivedRequestsAction(User $loggedInUser): JsonResponse
    {
        $requests = $this->friendRequestRepository->findPendingReceivedByUser($loggedInUser->getId());

        return new JsonResponse([
            'items' => array_map(
                fn (FriendRequest $friendRequest): array => [
                    'requestId' => $friendRequest->getId(),
                    'user' => $this->normalizeUser($friendRequest->getRequester()),
                ],
                $requests,
            ),
        ]);
    }

    #[Route(path: '/requests/sent', methods: [Request::METHOD_GET])]
    public function listSentRequestsAction(User $loggedInUser): JsonResponse
    {
        $requests = $this->friendRequestRepository->findPendingSentByUser($loggedInUser->getId());

        return new JsonResponse([
            'items' => array_map(
                fn (FriendRequest $friendRequest): array => [
                    'requestId' => $friendRequest->getId(),
                    'user' => $this->normalizeUser($friendRequest->getAddressee()),
                ],
                $requests,
            ),
        ]);
    }

    /** @return array<string, string> */
    private function normalizeUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];
    }
}
