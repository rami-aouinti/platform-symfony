<?php

declare(strict_types=1);

namespace App\User\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\User
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final class LoadRecruitmentUserData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var UserGroup $group */
        $group = $this->getReference('UserGroup-user', UserGroup::class);

        $user = (new User())
            ->setUsername('hugo-user')
            ->setFirstName('Hugo')
            ->setLastName('Recruitment')
            ->setEmail('hugo.user@test.com')
            ->setLanguage(Language::EN)
            ->setLocale(Locale::EN)
            ->setPlainPassword('password-user')
            ->addUserGroup($group);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('20000000-0000-1000-8000-000000000010'), $user);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('User-hugo-user', $user);
    }

    #[Override]
    public function getOrder(): int
    {
        return 4;
    }
}
