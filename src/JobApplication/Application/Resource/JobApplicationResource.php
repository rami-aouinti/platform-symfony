<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\JobApplication\Application\DTO\JobApplication\JobApplication as JobApplicationDto;
use App\JobApplication\Application\DTO\JobApplication\OfferApplicationPayload;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobApplication\Domain\Entity\JobApplication as Entity;
use App\JobApplication\Domain\Message\JobApplicationDecidedMessage;
use App\JobApplication\Domain\Message\ConversationEnsureForAcceptedApplicationMessage;
use App\JobApplication\Domain\Message\JobApplicationSubmittedMessage;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use App\JobApplication\Domain\Exception\JobApplicationException;
use App\JobApplication\Domain\Repository\Interfaces\JobApplicationRepositoryInterface as RepositoryInterface;
use App\JobOffer\Infrastructure\Repository\JobOfferRepository;
use App\User\Application\Security\Permission;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
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
        private readonly JobOfferRepository $jobOfferRepository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly MessageServiceInterface $messageService,
    ) {
        parent::__construct($repository);
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity || !$restDto instanceof JobApplicationDto) {
            return;
        }

        $user = $this->userTypeIdentification->getUser();

        if ($user instanceof User && $entity->getCandidate() === null) {
            $entity->setCandidate($user);
        }
    }

    public function apply(string $jobOfferId, ?OfferApplicationPayload $payload = null): Entity
    {
        $candidate = $this->getCurrentUser();
        $jobOffer = $this->jobOfferRepository->find($jobOfferId);

        if ($jobOffer === null) {
            throw new JobApplicationException('Job offer not found.', Response::HTTP_NOT_FOUND);
        }

        if (!$this->authorizationChecker->isGranted(Permission::JOB_APPLICATION_APPLY->value, $jobOffer)) {
            throw new JobApplicationException('Job offer not found.', Response::HTTP_NOT_FOUND);
        }

        $existing = $this->getRepository()->findOneBy([
            'jobOffer' => $jobOffer,
            'candidate' => $candidate,
        ]);

        if ($existing instanceof Entity) {
            throw new JobApplicationException('You have already applied to this job offer.', Response::HTTP_CONFLICT);
        }

        $application = (new Entity())
            ->setJobOffer($jobOffer)
            ->setCandidate($candidate)
            ->setCoverLetter($payload?->getCoverLetter())
            ->setCvUrl($payload?->getCvUrl())
            ->setAttachments($payload?->getAttachments())
            ->setStatus(JobApplicationStatus::PENDING);

        $this->getRepository()->save($application);

        $this->messageService->sendMessage(new JobApplicationSubmittedMessage(
            applicationId: $application->getId(),
            candidateUserId: $candidate->getId(),
            offerId: $jobOffer->getId(),
            offerOwnerOrCreatorUserId: $jobOffer->getCompany()?->getOwner()?->getId() ?? $jobOffer->getCreatedBy()?->getId(),
        ));

        return $application;
    }

    public function withdraw(string $applicationId): Entity
    {
        $application = $this->getByIdOrFail($applicationId);

        if (!$this->authorizationChecker->isGranted(Permission::JOB_APPLICATION_WITHDRAW->value, $application)) {
            throw new JobApplicationException('Only the candidate can withdraw this application.', Response::HTTP_FORBIDDEN);
        }

        $this->assertTransition($application, JobApplicationStatus::WITHDRAWN);

        $application
            ->setStatus(JobApplicationStatus::WITHDRAWN)
            ->setDecidedBy(null)
            ->setDecidedAt(null);

        $this->getRepository()->save($application);

        $candidate = $application->getCandidate();
        $jobOffer = $application->getJobOffer();

        if ($candidate instanceof User && $jobOffer !== null) {
            $this->messageService->sendMessage(new JobApplicationDecidedMessage(
                applicationId: $application->getId(),
                candidateUserId: $candidate->getId(),
                offerId: $jobOffer->getId(),
                status: JobApplicationStatus::WITHDRAWN->value,
            ));
        }

        return $application;
    }

    public function decide(string $applicationId, JobApplicationStatus $status): Entity
    {
        if (!in_array($status, [JobApplicationStatus::ACCEPTED, JobApplicationStatus::REJECTED], true)) {
            throw new JobApplicationException('Decision status must be accepted or rejected.', Response::HTTP_BAD_REQUEST);
        }

        $application = $this->getByIdOrFail($applicationId);
        $user = $this->getCurrentUser();
        if (!$this->authorizationChecker->isGranted(Permission::JOB_APPLICATION_DECIDE->value, $application)) {
            throw new JobApplicationException('Only the job offer owner or an authorized company manager can decide this application.', Response::HTTP_FORBIDDEN);
        }

        $this->assertTransition($application, $status);

        $application
            ->setStatus($status)
            ->setDecidedBy($user)
            ->setDecidedAt(new DateTimeImmutable());

        $this->getRepository()->save($application);

        $candidate = $application->getCandidate();
        $jobOffer = $application->getJobOffer();

        if ($candidate instanceof User && $jobOffer !== null) {
            $this->messageService->sendMessage(new JobApplicationDecidedMessage(
                applicationId: $application->getId(),
                candidateUserId: $candidate->getId(),
                offerId: $jobOffer->getId(),
                status: $status->value,
            ));

            if ($status === JobApplicationStatus::ACCEPTED) {
                $this->messageService->sendMessage(new ConversationEnsureForAcceptedApplicationMessage(
                    applicationId: $application->getId(),
                ));
            }
        }

        return $application;
    }

    public function findAllowedForCurrentUser(): array
    {
        return array_values(array_filter(
            $this->find(orderBy: ['createdAt' => 'DESC']),
            fn (Entity $application): bool => $this->authorizationChecker->isGranted(Permission::JOB_APPLICATION_VIEW->value, $application),
        ));
    }

    public function findForMyOffers(): array
    {
        return $this->getRepository()->findForMyOffers($this->getCurrentUser());
    }

    public function getAllowedForCurrentUser(string $applicationId): Entity
    {
        $application = $this->getByIdOrFail($applicationId);

        if (!$this->authorizationChecker->isGranted(Permission::JOB_APPLICATION_VIEW->value, $application)) {
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

    private function assertTransition(Entity $application, JobApplicationStatus $target): void
    {
        if (!$application->getStatus()->canTransitionTo($target)) {
            throw new JobApplicationException(
                'Invalid status transition from ' . $application->getStatus()->value . ' to ' . $target->value . '.',
                Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
