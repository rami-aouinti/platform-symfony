<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Resource;

use App\General\Application\Rest\RestResource;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobApplication\Domain\Entity\JobApplication as Entity;
use App\JobApplication\Domain\Enum\ApplicationStatus;
use App\JobApplication\Domain\Exception\JobApplicationException;
use App\JobApplication\Domain\Repository\Interfaces\JobApplicationRepositoryInterface as RepositoryInterface;
use App\Offer\Infrastructure\Repository\OfferRepository;
use App\User\Application\Security\Permission;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function array_filter;
use function array_values;
use function in_array;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class JobApplicationResource extends RestResource implements JobApplicationResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly OfferRepository $offerRepository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
        parent::__construct($repository);
    }

    public function apply(string $offerId): Entity
    {
        $user = $this->getCurrentUser();
        $offer = $this->offerRepository->find($offerId);

        if ($offer === null) {
            throw new JobApplicationException('Offer not found.', Response::HTTP_NOT_FOUND);
        }

        if (!$this->authorizationChecker->isGranted(Permission::OFFER_VIEW->value, $offer)) {
            throw new JobApplicationException('Offer not found.', Response::HTTP_NOT_FOUND);
        }

        $existing = $this->getRepository()->findOneBy([
            'offer' => $offer,
            'user' => $user,
        ]);

        if ($existing instanceof Entity) {
            throw new JobApplicationException(
                'You have already applied to this offer.',
                Response::HTTP_CONFLICT,
            );
        }

        $application = new Entity($offer, $user);
        $this->getRepository()->save($application);

        return $application;
    }

    public function withdraw(string $applicationId): Entity
    {
        $application = $this->getByIdOrFail($applicationId);

        if (!$this->authorizationChecker->isGranted(Permission::APPLICATION_WITHDRAW->value, $application)) {
            throw new JobApplicationException(
                'Only the applicant can withdraw this application.',
                Response::HTTP_FORBIDDEN,
            );
        }

        $this->assertTransition($application, ApplicationStatus::WITHDRAWN);

        $application->setStatus(ApplicationStatus::WITHDRAWN);
        $this->getRepository()->save($application);

        return $application;
    }

    public function decide(string $applicationId, ApplicationStatus $status): Entity
    {
        if (!in_array($status, [ApplicationStatus::ACCEPTED, ApplicationStatus::REJECTED], true)) {
            throw new JobApplicationException(
                'Decision status must be ACCEPTED or REJECTED.',
                Response::HTTP_BAD_REQUEST,
            );
        }

        $application = $this->getByIdOrFail($applicationId);

        if ($application->getOffer()->getCreatedBy()?->getId() !== $this->getCurrentUser()->getId()) {
            throw new JobApplicationException(
                'Only the offer creator can decide this application.',
                Response::HTTP_FORBIDDEN,
            );
        }

        $this->assertTransition($application, $status);

        $application->setStatus($status);
        $this->getRepository()->save($application);

        return $application;
    }

    public function findAllowedForCurrentUser(): array
    {
        return array_values(array_filter(
            $this->find(orderBy: ['createdAt' => 'DESC']),
            fn (Entity $application): bool => $this->authorizationChecker->isGranted(Permission::APPLICATION_VIEW->value, $application),
        ));
    }

    public function getAllowedForCurrentUser(string $applicationId): Entity
    {
        $application = $this->getByIdOrFail($applicationId);

        if (!$this->authorizationChecker->isGranted(Permission::APPLICATION_VIEW->value, $application)) {
            throw new JobApplicationException('Application not found.', Response::HTTP_NOT_FOUND);
        }

        return $application;
    }

    private function getByIdOrFail(string $applicationId): Entity
    {
        $application = $this->getRepository()->find($applicationId);

        if (!$application instanceof Entity) {
            throw new JobApplicationException('Application not found.', Response::HTTP_NOT_FOUND);
        }

        return $application;
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new JobApplicationException('Authenticated user not found.', Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    private function assertTransition(Entity $application, ApplicationStatus $target): void
    {
        if (!$application->getStatus()->canTransitionTo($target)) {
            throw new JobApplicationException(
                'Invalid status transition from ' . $application->getStatus()->value . ' to ' . $target->value . '.',
                Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
