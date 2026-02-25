<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Security\Voter;

use App\JobApplication\Domain\Entity\JobApplication;
use App\Offer\Domain\Entity\Offer;
use App\User\Application\Security\Permission;
use App\User\Application\Security\Permission\Interfaces\CompanyPermissionMatrixInterface;
use App\User\Application\Security\SecurityUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

/**
 * @extends Voter<string, mixed>
 */
class OfferApplicationPermissionVoter extends Voter
{
    public function __construct(
        private readonly CompanyPermissionMatrixInterface $companyPermissionMatrix,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            Permission::OFFER_VIEW->value,
            Permission::OFFER_MANAGE->value,
            Permission::APPLICATION_VIEW->value,
            Permission::APPLICATION_MANAGE->value,
            Permission::APPLICATION_DECIDE->value,
            Permission::APPLICATION_WITHDRAW->value,
        ], true)) {
            return false;
        }

        return $subject instanceof Offer || $subject instanceof JobApplication;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SecurityUser) {
            return false;
        }

        if ($subject instanceof Offer) {
            return $this->voteForOffer($user, $attribute, $subject);
        }

        if (!$subject instanceof JobApplication) {
            return false;
        }

        return $this->voteForApplication($user, $attribute, $subject);
    }

    private function voteForOffer(SecurityUser $user, string $attribute, Offer $offer): bool
    {
        if (!in_array($attribute, [Permission::OFFER_VIEW->value, Permission::OFFER_MANAGE->value], true)) {
            return false;
        }

        return $this->companyPermissionMatrix->isGranted(
            $user,
            $attribute,
            $offer->getCompany()?->getId(),
            $offer->getCreatedBy()?->getId() === $user->getUserIdentifier(),
        );
    }

    private function voteForApplication(SecurityUser $user, string $attribute, JobApplication $application): bool
    {
        $offer = $application->getOffer();
        $companyId = $offer->getCompany()?->getId();
        $isOfferOwner = $offer->getCreatedBy()?->getId() === $user->getUserIdentifier();

        if ($attribute === Permission::APPLICATION_WITHDRAW->value) {
            return $application->getUser()->getId() === $user->getUserIdentifier()
                || $this->companyPermissionMatrix->isGranted($user, $attribute, $companyId);
        }

        if ($attribute === Permission::APPLICATION_VIEW->value
            && $application->getUser()->getId() === $user->getUserIdentifier()) {
            return true;
        }

        return $this->companyPermissionMatrix->isGranted($user, $attribute, $companyId, $isOfferOwner);
    }
}
