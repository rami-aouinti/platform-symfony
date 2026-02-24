<?php

declare(strict_types=1);

namespace App\Notification\Application\Security\Voter;

use App\User\Application\Security\Permission;
use App\User\Application\Security\Permission\Interfaces\CompanyPermissionMatrixInterface;
use App\User\Application\Security\SecurityUser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;
use function is_string;

/**
 * @extends Voter<string, mixed>
 */
class NotificationPermissionVoter extends Voter
{
    public function __construct(
        private readonly CompanyPermissionMatrixInterface $companyPermissionMatrix,
        private readonly RequestStack $requestStack,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            Permission::NOTIFICATION_VIEW->value,
            Permission::NOTIFICATION_MANAGE->value,
        ], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SecurityUser) {
            return false;
        }

        return $this->companyPermissionMatrix->isGranted(
            $user,
            $attribute,
            $this->resolveCompanyId(),
        );
    }

    private function resolveCompanyId(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return null;
        }

        $companyId = $request->attributes->get('companyId')
            ?? $request->attributes->get('company_id')
            ?? $request->query->get('companyId')
            ?? $request->query->get('company_id');

        return is_string($companyId) ? $companyId : null;
    }
}
