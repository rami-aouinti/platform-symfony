<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Infrastructure\DataFixtures\ORM;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadUserApplicationData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $rows = [
            ['user' => 'john-root', 'application' => 'CRM', 'name' => 'CRM 1', 'keyName' => 'crm-1', 'public' => true, 'active' => true],
            ['user' => 'john-root', 'application' => 'CRM', 'name' => 'CRM 2', 'keyName' => 'crm-2', 'public' => false, 'active' => true],
            ['user' => 'john-root', 'application' => 'Shop', 'name' => 'Shop Principal', 'keyName' => 'shop-principal', 'public' => false, 'active' => true],
            ['user' => 'john-user', 'application' => 'Recruit', 'name' => 'Recruit John', 'keyName' => 'recruit-john', 'public' => false, 'active' => true],
            ['user' => 'alice-user', 'application' => 'School', 'name' => 'School Alice', 'keyName' => 'school-alice', 'public' => true, 'active' => true],
            ['user' => 'carol-user', 'application' => 'Shop', 'name' => 'Shop Carol', 'keyName' => 'shop-carol', 'public' => false, 'active' => true],
            ['user' => 'hugo-user', 'application' => 'CRM', 'name' => 'CRM Hugo', 'keyName' => 'crm-hugo', 'public' => true, 'active' => false],
        ];

        foreach ($rows as $index => $row) {
            $user = $this->getReference('User-' . $row['user'], User::class);
            $application = $this->getReference('Application-' . $row['application'], Application::class);

            $userApplication = (new UserApplication($user, $application))
                ->setName($row['name'])
                ->setKeyName($row['keyName'])
                ->setPublic($row['public'])
                ->setActive($row['active']);

            $manager->persist($userApplication);
            $this->addReference('UserApplication-' . $row['user'] . '-' . $index, $userApplication);

            if ($row['user'] === 'john-root' && !$this->hasReference('UserApplication-john-root-' . $row['application'], UserApplication::class)) {
                $this->addReference('UserApplication-john-root-' . $row['application'], $userApplication);
            }
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 4;
    }
}
