<?php

declare(strict_types=1);

namespace App\User\Application\Security\Voter;

use App\User\Application\Security\Permission;
use App\User\Application\Security\Permission\Interfaces\CompanyPermissionMatrixInterface;
use App\User\Application\Security\SecurityUser;
use App\User\Domain\Entity\User;
use Override;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function is_array;
use function is_string;

/**
 * @package App\User
 *
 * @template TAttribute of string
 * @template TSubject of mixed
 *
 * @extends Voter<TAttribute, TSubject>
 */
class IsUserHimselfVoter extends Voter
{
    public const string ATTRIBUTE = 'IS_USER_HIMSELF';

    public function __construct(
        private readonly CompanyPermissionMatrixInterface $companyPermissionMatrix,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ATTRIBUTE && ($subject instanceof User || is_array($subject));
    }

    #[Override]
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        $securityUser = $token->getUser();

        if (!($securityUser instanceof SecurityUser)) {
            return false;
        }

        $targetUser = $subject instanceof User ? $subject : ($subject['user'] ?? null);

        if (!($targetUser instanceof User)) {
            return false;
        }

        $isOwner = $securityUser->getUuid() === $targetUser->getId();

        return $this->companyPermissionMatrix->isGranted(
            $securityUser,
            Permission::CRM_VIEW,
            $this->resolveCompanyId($subject),
            $isOwner,
        );
    }

    private function resolveCompanyId(mixed $subject): ?string
    {
        if (is_array($subject)) {
            $companyId = $subject['company_id'] ?? $subject['companyId'] ?? null;

            return is_string($companyId) ? $companyId : null;
        }

        return $this->requestStack->getCurrentRequest()?->headers->get('X-Company-Id');
    }
}
