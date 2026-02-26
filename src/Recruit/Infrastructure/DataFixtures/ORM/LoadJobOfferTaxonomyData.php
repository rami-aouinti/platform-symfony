<?php

declare(strict_types=1);

namespace App\Recruit\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\General\Domain\Entity\City;
use App\Recruit\Domain\Entity\JobCategory;
use App\Recruit\Domain\Entity\Language;
use App\General\Domain\Entity\Region;
use App\Recruit\Domain\Entity\Skill;
use App\Tests\Utils\PhpUnitUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final class LoadJobOfferTaxonomyData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $skills = [
            'Skill-php' => [
                'uuid' => '61000000-0000-1000-8000-000000000001',
                'name' => 'PHP',
            ],
            'Skill-symfony' => [
                'uuid' => '61000000-0000-1000-8000-000000000002',
                'name' => 'Symfony',
            ],
            'Skill-devops' => [
                'uuid' => '61000000-0000-1000-8000-000000000003',
                'name' => 'DevOps',
            ],
            'Skill-aws' => [
                'uuid' => '61000000-0000-1000-8000-000000000004',
                'name' => 'AWS',
            ],
            'Skill-react' => [
                'uuid' => '61000000-0000-1000-8000-000000000005',
                'name' => 'React',
            ],
            'Skill-sql' => [
                'uuid' => '61000000-0000-1000-8000-000000000006',
                'name' => 'SQL',
            ],
            'Skill-git' => [
                'uuid' => '61000000-0000-1000-8000-000000000007',
                'name' => 'Git',
            ],
            'Skill-docker' => [
                'uuid' => '61000000-0000-1000-8000-000000000008',
                'name' => 'Docker',
            ],
            'Skill-kubernetes' => [
                'uuid' => '61000000-0000-1000-8000-000000000009',
                'name' => 'Kubernetes',
            ],
            'Skill-terraform' => [
                'uuid' => '61000000-0000-1000-8000-000000000010',
                'name' => 'Terraform',
            ],
            'Skill-postgresql' => [
                'uuid' => '61000000-0000-1000-8000-000000000011',
                'name' => 'PostgreSQL',
            ],
            'Skill-python' => [
                'uuid' => '61000000-0000-1000-8000-000000000012',
                'name' => 'Python',
            ],
            'Skill-nodejs' => [
                'uuid' => '61000000-0000-1000-8000-000000000013',
                'name' => 'Node.js',
            ],
            'Skill-typescript' => [
                'uuid' => '61000000-0000-1000-8000-000000000014',
                'name' => 'TypeScript',
            ],
            'Skill-elasticsearch' => [
                'uuid' => '61000000-0000-1000-8000-000000000015',
                'name' => 'Elasticsearch',
            ],
        ];

        foreach ($skills as $reference => $data) {
            $skill = (new Skill())->setName($data['name']);
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($data['uuid']), $skill);
            $manager->persist($skill);
            $this->addReference($reference, $skill);
        }

        $languages = [
            'Language-fr' => [
                'uuid' => '62000000-0000-1000-8000-000000000001',
                'code' => 'fr',
                'name' => 'Français',
            ],
            'Language-en' => [
                'uuid' => '62000000-0000-1000-8000-000000000002',
                'code' => 'en',
                'name' => 'English',
            ],
            'Language-de' => [
                'uuid' => '62000000-0000-1000-8000-000000000003',
                'code' => 'de',
                'name' => 'Deutsch',
            ],
            'Language-es' => [
                'uuid' => '62000000-0000-1000-8000-000000000004',
                'code' => 'es',
                'name' => 'Español',
            ],
            'Language-it' => [
                'uuid' => '62000000-0000-1000-8000-000000000005',
                'code' => 'it',
                'name' => 'Italiano',
            ],
            'Language-pt' => [
                'uuid' => '62000000-0000-1000-8000-000000000006',
                'code' => 'pt',
                'name' => 'Português',
            ],
            'Language-nl' => [
                'uuid' => '62000000-0000-1000-8000-000000000007',
                'code' => 'nl',
                'name' => 'Nederlands',
            ],
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
            'JobCategory-backend' => [
                'uuid' => '65000000-0000-1000-8000-000000000001',
                'code' => 'backend',
                'name' => 'Backend Engineering',
            ],
            'JobCategory-platform' => [
                'uuid' => '65000000-0000-1000-8000-000000000002',
                'code' => 'platform',
                'name' => 'Platform & SRE',
            ],
            'JobCategory-data' => [
                'uuid' => '65000000-0000-1000-8000-000000000003',
                'code' => 'data',
                'name' => 'Data Engineering',
            ],
            'JobCategory-frontend' => [
                'uuid' => '65000000-0000-1000-8000-000000000004',
                'code' => 'frontend',
                'name' => 'Frontend Engineering',
            ],
            'JobCategory-mobile' => [
                'uuid' => '65000000-0000-1000-8000-000000000005',
                'code' => 'mobile',
                'name' => 'Mobile Development',
            ],
            'JobCategory-qa' => [
                'uuid' => '65000000-0000-1000-8000-000000000006',
                'code' => 'qa',
                'name' => 'Quality Assurance',
            ],
            'JobCategory-product' => [
                'uuid' => '65000000-0000-1000-8000-000000000007',
                'code' => 'product',
                'name' => 'Product Management',
            ],
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
            'Region-idf' => [
                'uuid' => '63000000-0000-1000-8000-000000000001',
                'code' => 'IDF',
                'name' => 'Île-de-France',
                'countryCode' => 'FR',
            ],
            'Region-berlin' => [
                'uuid' => '63000000-0000-1000-8000-000000000002',
                'code' => 'BE',
                'name' => 'Berlin',
                'countryCode' => 'DE',
            ],
            'Region-ara' => [
                'uuid' => '63000000-0000-1000-8000-000000000003',
                'code' => 'ARA',
                'name' => 'Auvergne-Rhône-Alpes',
                'countryCode' => 'FR',
            ],
            'Region-occitanie' => [
                'uuid' => '63000000-0000-1000-8000-000000000004',
                'code' => 'OCC',
                'name' => 'Occitanie',
                'countryCode' => 'FR',
            ],
            'Region-catalonia' => [
                'uuid' => '63000000-0000-1000-8000-000000000005',
                'code' => 'CT',
                'name' => 'Catalonia',
                'countryCode' => 'ES',
            ],
            'Region-madrid' => [
                'uuid' => '63000000-0000-1000-8000-000000000006',
                'code' => 'MD',
                'name' => 'Community of Madrid',
                'countryCode' => 'ES',
            ],
            'Region-lombardy' => [
                'uuid' => '63000000-0000-1000-8000-000000000007',
                'code' => 'LOM',
                'name' => 'Lombardy',
                'countryCode' => 'IT',
            ],
            'Region-north-holland' => [
                'uuid' => '63000000-0000-1000-8000-000000000008',
                'code' => 'NH',
                'name' => 'North Holland',
                'countryCode' => 'NL',
            ],
            'Region-lisbon' => [
                'uuid' => '63000000-0000-1000-8000-000000000009',
                'code' => 'LIS',
                'name' => 'Lisbon District',
                'countryCode' => 'PT',
            ],
            'Region-sao-paulo' => [
                'uuid' => '63000000-0000-1000-8000-000000000010',
                'code' => 'SP',
                'name' => 'São Paulo',
                'countryCode' => 'BR',
            ],
            'Region-quebec' => [
                'uuid' => '63000000-0000-1000-8000-000000000011',
                'code' => 'QC',
                'name' => 'Québec',
                'countryCode' => 'CA',
            ],
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

        $cities = [
            'City-paris' => [
                'uuid' => '64000000-0000-1000-8000-000000000001',
                'name' => 'Paris',
                'regionReference' => 'Region-idf',
            ],
            'City-berlin' => [
                'uuid' => '64000000-0000-1000-8000-000000000002',
                'name' => 'Berlin',
                'regionReference' => 'Region-berlin',
            ],
            'City-lyon' => [
                'uuid' => '64000000-0000-1000-8000-000000000003',
                'name' => 'Lyon',
                'regionReference' => 'Region-ara',
            ],
            'City-toulouse' => [
                'uuid' => '64000000-0000-1000-8000-000000000004',
                'name' => 'Toulouse',
                'regionReference' => 'Region-occitanie',
            ],
            'City-barcelona' => [
                'uuid' => '64000000-0000-1000-8000-000000000005',
                'name' => 'Barcelona',
                'regionReference' => 'Region-catalonia',
            ],
            'City-madrid' => [
                'uuid' => '64000000-0000-1000-8000-000000000006',
                'name' => 'Madrid',
                'regionReference' => 'Region-madrid',
            ],
            'City-milan' => [
                'uuid' => '64000000-0000-1000-8000-000000000007',
                'name' => 'Milan',
                'regionReference' => 'Region-lombardy',
            ],
            'City-amsterdam' => [
                'uuid' => '64000000-0000-1000-8000-000000000008',
                'name' => 'Amsterdam',
                'regionReference' => 'Region-north-holland',
            ],
            'City-lisbon' => [
                'uuid' => '64000000-0000-1000-8000-000000000009',
                'name' => 'Lisbon',
                'regionReference' => 'Region-lisbon',
            ],
            'City-montreal' => [
                'uuid' => '64000000-0000-1000-8000-000000000010',
                'name' => 'Montreal',
                'regionReference' => 'Region-quebec',
            ],
            'City-sao-paulo' => [
                'uuid' => '64000000-0000-1000-8000-000000000011',
                'name' => 'São Paulo',
                'regionReference' => 'Region-sao-paulo',
            ],
        ];

        foreach ($cities as $reference => $data) {
            /** @var Region $region */
            $region = $this->getReference($data['regionReference'], Region::class);
            $city = (new City())
                ->setName($data['name'])
                ->setRegion($region);
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
