<?php

declare(strict_types=1);

namespace App\General\Transport\EventListener;

use App\Company\Domain\Entity\Company;
use App\Task\Domain\Entity\Project;
use App\User\Domain\Entity\UserAvatar;
use App\User\Domain\Entity\UserProfile;
use Doctrine\Persistence\Event\LifecycleEventArgs;

use function rawurlencode;
use function sprintf;
use function trim;

class DefaultPhotoEntityEventListener
{
    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->process($event);
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->process($event);
    }

    private function process(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Company) {
            $this->ensureCompanyPhotoUrl($entity);

            return;
        }

        if ($entity instanceof Project) {
            $this->ensureProjectPhotoUrl($entity);

            return;
        }

        if ($entity instanceof UserProfile) {
            $this->ensureUserProfilePhotoUrl($entity);
        }
    }

    private function ensureCompanyPhotoUrl(Company $company): void
    {
        $photoUrl = trim((string)$company->getStoredPhotoUrl());

        if ($photoUrl === '') {
            $company->setPhotoUrl($this->buildAvatarUrl($company->getLegalName()));
        }
    }

    private function ensureProjectPhotoUrl(Project $project): void
    {
        $photoUrl = trim((string)$project->getStoredPhotoUrl());

        if ($photoUrl === '') {
            $project->setPhotoUrl($this->buildAvatarUrl($project->getName()));
        }
    }

    private function ensureUserProfilePhotoUrl(UserProfile $profile): void
    {
        $avatar = $profile->getAvatar();

        if (!$avatar instanceof UserAvatar) {
            $profile->setAvatar(
                (new UserAvatar($profile))
                    ->setUrl($this->buildAvatarUrl($profile->getUser()->getUsername())),
            );

            return;
        }

        $avatarUrl = trim($avatar->getUrl());

        if ($avatarUrl === '') {
            $avatar->setUrl($this->buildAvatarUrl($profile->getUser()->getUsername()));
        }
    }

    private function buildAvatarUrl(string $name): string
    {
        return sprintf(
            'https://ui-avatars.com/api/?name=%s&format=png',
            rawurlencode($name),
        );
    }
}
