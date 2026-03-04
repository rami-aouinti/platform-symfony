<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Service;

use App\ApplicationCatalog\Application\Service\Interfaces\UserApplicationCreateServiceInterface;
use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\ApplicationCatalog\Domain\Repository\Interfaces\UserApplicationRepositoryInterface;
use App\User\Domain\Entity\User;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class UserApplicationCreateService implements UserApplicationCreateServiceInterface
{
    public function __construct(
        private UserApplicationRepositoryInterface $userApplicationRepository,
        private SluggerInterface $slugger,
    ) {
    }

    public function create(User $user, Application $application, ?string $name, ?string $logo, ?string $description, bool $public): UserApplication
    {
        $resolvedName = trim((string)$name);

        if ($resolvedName === '') {
            $resolvedName = $application->getName();
        }

        $entity = (new UserApplication($user, $application))
            ->setName($resolvedName)
            ->setLogo($logo)
            ->setDescription($description)
            ->setPublic($public)
            ->setKeyName($this->generateUniqueKeyName($resolvedName));

        $user->addUserApplication($entity);
        $this->userApplicationRepository->save($entity);

        return $entity;
    }

    public function generateUniqueKeyName(string $name, ?string $excludeUserApplicationId = null): string
    {
        $baseKeyName = strtolower($this->slugger->slug($name)->toString());

        if ($baseKeyName === '') {
            $baseKeyName = 'user-application';
        }

        $candidate = $baseKeyName;
        $index = 2;

        while (true) {
            $existing = $this->userApplicationRepository->findOneByKeyName($candidate);

            if (!$existing instanceof UserApplication || $existing->getId() === $excludeUserApplicationId) {
                break;
            }

            $candidate = sprintf('%s-%d', $baseKeyName, $index);
            ++$index;
        }

        return $candidate;
    }
}
