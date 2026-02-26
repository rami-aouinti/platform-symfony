<?php

declare(strict_types=1);

namespace App\Recruit\Application\Security\Voter;

use App\Recruit\Domain\Entity\JobOffer;
use App\User\Application\Security\Permission;
use App\User\Application\Security\Permission\Interfaces\CompanyPermissionMatrixInterface;
use App\User\Application\Security\SecurityUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

/**
 * @extends Voter<string, mixed>
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class JobOfferVoter extends Voter
{
    public function __construct(
        private readonly CompanyPermissionMatrixInterface $companyPermissionMatrix,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (
            !in_array($attribute, [
                Permission::JOB_OFFER_VIEW->value,
                Permission::JOB_OFFER_MANAGE->value,
            ], true)
        ) {
            return false;
        }

        return $subject instanceof JobOffer;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SecurityUser || !$subject instanceof JobOffer) {
            return false;
        }

        return $this->companyPermissionMatrix->isGranted(
            $user,
            $attribute,
            $subject->getCompany()?->getId(),
            $subject->getCreatedBy()?->getId() === $user->getUserIdentifier(),
        );
    }
}
