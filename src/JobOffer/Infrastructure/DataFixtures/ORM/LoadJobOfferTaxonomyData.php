<?php

declare(strict_types=1);

namespace App\JobOffer\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\JobOffer\Domain\Entity\City;
use App\JobOffer\Domain\Entity\JobCategory;
use App\JobOffer\Domain\Entity\Language;
use App\JobOffer\Domain\Entity\Region;
use App\JobOffer\Domain\Entity\Skill;
use App\Tests\Utils\PhpUnitUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadJobOfferTaxonomyData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $skills = [
            'Skill-php' => ['uuid' => '61000000-0000-1000-8000-000000000001', 'name' => 'PHP'],
            'Skill-symfony' => ['uuid' => '61000000-0000-1000-8000-000000000002', 'name' => 'Symfony'],
            'Skill-devops' => ['uuid' => '61000000-0000-1000-8000-000000000003', 'name' => 'DevOps'],
            'Skill-aws' => ['uuid' => '61000000-0000-1000-8000-000000000004', 'name' => 'AWS'],
            'Skill-react' => ['uuid' => '61000000-0000-1000-8000-000000000005', 'name' => 'React'],
            'Skill-sql' => ['uuid' => '61000000-0000-1000-8000-000000000006', 'name' => 'SQL'],
        ];

        foreach ($skills as $reference => $data) {
            $skill = (new Skill())->setName($data['name']);
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($data['uuid']), $skill);
            $manager->persist($skill);
            $this->addReference($reference, $skill);
        }

        $languages = [
            'Language-fr' => ['uuid' => '62000000-0000-1000-8000-000000000001', 'code' => 'fr', 'name' => 'Français'],
            'Language-en' => ['uuid' => '62000000-0000-1000-8000-000000000002', 'code' => 'en', 'name' => 'English'],
            'Language-de' => ['uuid' => '62000000-0000-1000-8000-000000000003', 'code' => 'de', 'name' => 'Deutsch'],
        ];

        foreach ($languages as $reference => $data) {
            $language = (new Language())
                ->setCode($data['code'])
                ->setName($data['name']);
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($data['uuid']), $language);
            $manager->persist($language);
            $this->addReference($reference, $language);
        }

        $jobCategories = [
            'JobCategory-backend' => ['uuid' => '65000000-0000-1000-8000-000000000001', 'code' => 'backend', 'name' => 'Backend Engineering'],
            'JobCategory-platform' => ['uuid' => '65000000-0000-1000-8000-000000000002', 'code' => 'platform', 'name' => 'Platform & SRE'],
            'JobCategory-data' => ['uuid' => '65000000-0000-1000-8000-000000000003', 'code' => 'data', 'name' => 'Data Engineering'],
            'JobCategory-frontend' => ['uuid' => '65000000-0000-1000-8000-000000000004', 'code' => 'frontend', 'name' => 'Frontend Engineering'],
        ];

        foreach ($jobCategories as $reference => $data) {
            $jobCategory = (new JobCategory())
                ->setCode($data['code'])
                ->setName($data['name']);
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($data['uuid']), $jobCategory);
            $manager->persist($jobCategory);
            $this->addReference($reference, $jobCategory);
        }

        $regions = [
            'Region-idf' => ['uuid' => '63000000-0000-1000-8000-000000000001', 'code' => 'IDF', 'name' => 'Île-de-France', 'countryCode' => 'FR'],
            'Region-berlin' => ['uuid' => '63000000-0000-1000-8000-000000000002', 'code' => 'BE', 'name' => 'Berlin', 'countryCode' => 'DE'],
            'Region-ara' => ['uuid' => '63000000-0000-1000-8000-000000000003', 'code' => 'ARA', 'name' => 'Auvergne-Rhône-Alpes', 'countryCode' => 'FR'],
            'Region-occitanie' => ['uuid' => '63000000-0000-1000-8000-000000000004', 'code' => 'OCC', 'name' => 'Occitanie', 'countryCode' => 'FR'],
        ];

        foreach ($regions as $reference => $data) {
            $region = (new Region())
                ->setCode($data['code'])
                ->setName($data['name'])
                ->setCountryCode($data['countryCode']);
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($data['uuid']), $region);
            $manager->persist($region);
            $this->addReference($reference, $region);
        }

        /** @var Region $idf */
        $idf = $this->getReference('Region-idf', Region::class);
        /** @var Region $berlin */
        $berlin = $this->getReference('Region-berlin', Region::class);
        /** @var Region $ara */
        $ara = $this->getReference('Region-ara', Region::class);
        /** @var Region $occitanie */
        $occitanie = $this->getReference('Region-occitanie', Region::class);

        $cities = [
            'City-paris' => ['uuid' => '64000000-0000-1000-8000-000000000001', 'name' => 'Paris', 'region' => $idf],
            'City-berlin' => ['uuid' => '64000000-0000-1000-8000-000000000002', 'name' => 'Berlin', 'region' => $berlin],
            'City-lyon' => ['uuid' => '64000000-0000-1000-8000-000000000003', 'name' => 'Lyon', 'region' => $ara],
            'City-toulouse' => ['uuid' => '64000000-0000-1000-8000-000000000004', 'name' => 'Toulouse', 'region' => $occitanie],
        ];

        foreach ($cities as $reference => $data) {
            $city = (new City())
                ->setName($data['name'])
                ->setRegion($data['region']);
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($data['uuid']), $city);
            $manager->persist($city);
            $this->addReference($reference, $city);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 5;
    }
}
