<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Security\Voter;

use App\JobApplication\Domain\Entity\JobApplication;
use App\JobOffer\Domain\Entity\JobOffer;
use App\User\Application\Security\Permission;
use App\User\Application\Security\Permission\Interfaces\CompanyPermissionMatrixInterface;
use App\User\Application\Security\SecurityUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

/**
 * @extends Voter<string, mixed>
 */
class JobApplicationVoter extends Voter
{
    public function __construct(
        private readonly CompanyPermissionMatrixInterface $companyPermissionMatrix,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === Permission::JOB_APPLICATION_APPLY->value) {
            return $subject instanceof JobOffer;
        }

        if (!in_array($attribute, [
            Permission::JOB_APPLICATION_VIEW->value,
            Permission::JOB_APPLICATION_DECIDE->value,
            Permission::JOB_APPLICATION_WITHDRAW->value,
        ], true)) {
            return false;
        }

        return $subject instanceof JobApplication;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SecurityUser) {
            return false;
        }

        if ($subject instanceof JobOffer) {
            return $this->companyPermissionMatrix->isGranted(
                $user,
                Permission::JOB_APPLICATION_APPLY,
                $subject->getCompany()?->getId(),
            );
        }

        if (!$subject instanceof JobApplication) {
            return false;
        }

        $offer = $subject->getJobOffer();
        $companyId = $offer?->getCompany()?->getId();
        $isOfferOwner = $offer?->getCreatedBy()?->getId() === $user->getUserIdentifier();

        if ($attribute === Permission::JOB_APPLICATION_WITHDRAW->value) {
            return $subject->getCandidate()?->getId() === $user->getUserIdentifier();
        }

        if ($attribute === Permission::JOB_APPLICATION_VIEW->value
            && $subject->getCandidate()?->getId() === $user->getUserIdentifier()) {
            return true;
        }

        return $this->companyPermissionMatrix->isGranted($user, $attribute, $companyId, $isOfferOwner);
    }
}
