<?php

declare(strict_types=1);

namespace App\Tests\Unit\Resume\Application\Security\Voter;

use App\Resume\Application\Security\Voter\ResumeVoter;
use App\Resume\Domain\Entity\Resume;
use App\User\Application\Security\Permission;
use App\User\Application\Security\SecurityUser;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ResumeVoterTest extends TestCase
{
    public function testOwnerCanViewEditDeleteAndUseResume(): void
    {
        $owner = new User();
        $resume = (new Resume())->setOwner($owner)->setIsPublic(false);
        $voter = new ResumeVoter();
        $token = $this->createToken(new SecurityUser($owner, ['ROLE_USER']));

        self::assertTrue($voter->vote($token, $resume, [Permission::RESUME_VIEW->value]) > 0);
        self::assertTrue($voter->vote($token, $resume, [Permission::RESUME_EDIT->value]) > 0);
        self::assertTrue($voter->vote($token, $resume, [Permission::RESUME_DELETE->value]) > 0);
        self::assertTrue($voter->vote($token, $resume, [Permission::RESUME_USE_FOR_APPLICATION->value]) > 0);
    }

    public function testOtherUserCanOnlyViewPublicResume(): void
    {
        $owner = new User();
        $other = new User();
        $voter = new ResumeVoter();
        $token = $this->createToken(new SecurityUser($other, ['ROLE_USER']));

        $publicResume = (new Resume())->setOwner($owner)->setIsPublic(true);
        $privateResume = (new Resume())->setOwner($owner)->setIsPublic(false);

        self::assertTrue($voter->vote($token, $publicResume, [Permission::RESUME_VIEW->value]) > 0);
        self::assertTrue($voter->vote($token, $privateResume, [Permission::RESUME_VIEW->value]) < 0);
        self::assertTrue($voter->vote($token, $publicResume, [Permission::RESUME_EDIT->value]) < 0);
        self::assertTrue($voter->vote($token, $publicResume, [Permission::RESUME_USE_FOR_APPLICATION->value]) < 0);
    }

    public function testAdminCanManageAnyResume(): void
    {
        $owner = new User();
        $admin = new User();
        $resume = (new Resume())->setOwner($owner)->setIsPublic(false);
        $voter = new ResumeVoter();
        $token = $this->createToken(new SecurityUser($admin, ['ROLE_ADMIN']));

        self::assertTrue($voter->vote($token, $resume, [Permission::RESUME_VIEW->value]) > 0);
        self::assertTrue($voter->vote($token, $resume, [Permission::RESUME_EDIT->value]) > 0);
        self::assertTrue($voter->vote($token, $resume, [Permission::RESUME_DELETE->value]) > 0);
    }

    private function createToken(SecurityUser $user): TokenInterface
    {
        return new class($user) implements TokenInterface {
            public function __construct(
                private readonly SecurityUser $user
            ) {
            }

            public function __toString(): string
            {
                return '';
            }

            public function getRoleNames(): array
            {
                return $this->user->getRoles();
            }

            public function getCredentials(): mixed
            {
                return null;
            }

            public function getUser(): mixed
            {
                return $this->user;
            }

            public function setUser(mixed $user): void
            {
            }

            public function getUserIdentifier(): string
            {
                return $this->user->getUserIdentifier();
            }

            public function isAuthenticated(): bool
            {
                return true;
            }

            public function setAuthenticated(bool $isAuthenticated): void
            {
            }

            public function eraseCredentials(): void
            {
            }

            public function getAttributes(): array
            {
                return [];
            }

            public function setAttributes(array $attributes): void
            {
            }

            public function hasAttribute(string $name): bool
            {
                return false;
            }

            public function getAttribute(string $name): mixed
            {
                return null;
            }

            public function setAttribute(string $name, mixed $value): void
            {
            }
        };
    }
}
