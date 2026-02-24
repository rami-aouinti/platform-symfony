<?php

declare(strict_types=1);

namespace App\Notification\Transport\Controller\Api\V1\Notification;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\Notification\Application\Resource\Interfaces\NotificationResourceInterface;
use App\Notification\Application\Resource\NotificationResource;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method NotificationResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/notifications')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Notification Management')]
class NotificationController extends Controller
{
    public function __construct(NotificationResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function findAction(Request $request, User $loggedInUser): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->find(criteria: [
                'user' => $loggedInUser->getId(),
            ]),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_GET])]
    public function findOneAction(Request $request, string $id, User $loggedInUser): Response
    {
        $notification = $this->getResource()->findOne($id, true);

        if ($notification->getUser()->getId() !== $loggedInUser->getId()) {
            throw $this->createAccessDeniedException();
        }

        return $this->getResponseHandler()->createResponse(
            $request,
            $notification,
            $this->getResource(),
        );
    }
}
