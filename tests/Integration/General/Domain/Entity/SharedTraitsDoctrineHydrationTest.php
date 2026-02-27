<?php

declare(strict_types=1);

namespace App\Tests\Integration\General\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Enum\CompanyStatus;
use App\General\Domain\Entity\City;
use App\General\Domain\Entity\Region;
use App\Media\Domain\Entity\Media;
use App\Media\Domain\Enum\MediaStatus;
use App\Recruit\Domain\Entity\JobCategory;
use App\Recruit\Domain\Entity\Language;
use App\Recruit\Domain\Entity\Offer;
use App\Recruit\Domain\Entity\Skill;
use App\Recruit\Domain\Enum\OfferStatus;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SharedTraitsDoctrineHydrationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    #[TestDox('Doctrine metadata keeps shared SQL column names and local override options.')]
    public function testDoctrineMetadataColumnConventions(): void
    {
        $this->assertColumn(Company::class, 'slug', 'slug', 255, false);
        $this->assertColumn(Company::class, 'status', 'status', 64, false);
        $this->assertColumn(Offer::class, 'description', 'description', null, false);
        $this->assertColumn(Offer::class, 'status', 'status', 64, false);
        $this->assertColumn(Media::class, 'name', 'name', 255, false);
        $this->assertColumn(Media::class, 'status', 'status', 64, false);
        $this->assertColumn(Skill::class, 'name', 'name', 100, false);
        $this->assertColumn(JobCategory::class, 'name', 'name', 128, false);
        $this->assertColumn(Language::class, 'name', 'name', 100, false);
        $this->assertColumn(Region::class, 'name', 'name', 128, false);
        $this->assertColumn(City::class, 'name', 'name', 128, false);
    }

    #[TestDox('Doctrine-style field hydration remains functionally equivalent for shared traits.')]
    public function testDoctrineStyleHydrationForSharedFields(): void
    {
        $this->assertHydration(Company::class, 'slug', 'acme', 'getSlug');
        $this->assertHydration(Company::class, 'status', CompanyStatus::ACTIVE, 'getStatus');
        $this->assertHydration(Offer::class, 'description', 'Offer body', 'getDescription');
        $this->assertHydration(Offer::class, 'status', OfferStatus::DRAFT, 'getStatus');
        $this->assertHydration(Media::class, 'name', 'logo', 'getName');
        $this->assertHydration(Media::class, 'status', MediaStatus::ACTIVE, 'getStatus');
        $this->assertHydration(Skill::class, 'name', 'PHP', 'getName');
        $this->assertHydration(JobCategory::class, 'name', 'Engineering', 'getName');
        $this->assertHydration(Language::class, 'name', 'Français', 'getName');
        $this->assertHydration(Region::class, 'name', 'Île-de-France', 'getName');
        $this->assertHydration(City::class, 'name', 'Paris', 'getName');
    }

    private function assertColumn(string $className, string $field, string $columnName, ?int $length, bool $nullable): void
    {
        $metadata = $this->entityManager->getClassMetadata($className);
        $mapping = $metadata->getFieldMapping($field);

        self::assertSame($columnName, $mapping->columnName);
        self::assertSame($length, $mapping->length);
        self::assertSame($nullable, $mapping->nullable);
    }

    private function assertHydration(string $className, string $field, mixed $value, string $getter): void
    {
        $metadata = $this->entityManager->getClassMetadata($className);
        $entity = $metadata->newInstance();
        $metadata->setFieldValue($entity, $field, $value);

        self::assertSame($value, $entity->{$getter}());
    }
}
