<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Resource;

use App\Company\Domain\Entity\CompanyMembership;
use App\Company\Infrastructure\Repository\CompanyMembershipRepository;
use App\General\Application\Rest\RestResource;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobApplication\Domain\Entity\JobApplication as Entity;
use App\JobApplication\Domain\Enum\ApplicationStatus;
use App\JobApplication\Domain\Exception\JobApplicationException;
use App\JobApplication\Domain\Repository\Interfaces\JobApplicationRepositoryInterface as RepositoryInterface;
use App\Offer\Infrastructure\Repository\OfferRepository;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;

use function array_filter;
use function in_array;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class JobApplicationResource extends RestResource implements JobApplicationResourceInterface
{
    /**
     * @var array<int, string>
     */
    private const array MANAGER_ROLES = [
        CompanyMembership::ROLE_OWNER,
        CompanyMembership::ROLE_CRM_MANAGER,
    ];

    public function __construct(
        RepositoryInterface $repository,
        private readonly OfferRepository $offerRepository,
        private readonly CompanyMembershipRepository $companyMembershipRepository,
        private readonly UserTypeIdentification $userTypeIdentification,
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
        $application = $this->getAllowedForCurrentUser($applicationId);
        $user = $this->getCurrentUser();

        if ($application->getUser()->getId() !== $user->getId()) {
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

        if (!$this->canDecide($application, $this->getCurrentUser())) {
            throw new JobApplicationException(
                'Only the offer creator or a company owner/manager can decide this application.',
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
        $user = $this->getCurrentUser();

        return array_values(array_filter(
            $this->find(orderBy: ['createdAt' => 'DESC']),
            fn (Entity $application): bool => $this->canView($application, $user),
        ));
    }

    public function getAllowedForCurrentUser(string $applicationId): Entity
    {
        $application = $this->getByIdOrFail($applicationId);

        if (!$this->canView($application, $this->getCurrentUser())) {
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

    private function canView(Entity $application, User $user): bool
    {
        if ($application->getUser()->getId() === $user->getId()) {
            return true;
        }

        return $this->canDecide($application, $user);
    }

    private function canDecide(Entity $application, User $user): bool
    {
        if ($application->getOffer()->getCreatedBy()?->getId() === $user->getId()) {
            return true;
        }

        $company = $application->getOffer()->getCompany();

        if ($company->getOwner()?->getId() === $user->getId()) {
            return true;
        }

        $membership = $this->companyMembershipRepository->findOneBy([
            'company' => $company,
            'user' => $user,
        ]);

        return $membership instanceof CompanyMembership
            && in_array($membership->getRole(), self::MANAGER_ROLES, true);
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
