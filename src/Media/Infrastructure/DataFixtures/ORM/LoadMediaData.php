<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Media\Domain\Entity\Media;
use App\Media\Domain\Entity\MediaFolder;
use App\Media\Domain\Enum\MediaStatus;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\Media\Infrastructure\DataFixtures\ORM
 */
final class LoadMediaData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $john */
        $john = $this->getReference('User-john-user', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);

        /** @var MediaFolder $johnImages */
        $johnImages = $this->getReference('MediaFolder-john-user-images', MediaFolder::class);
        /** @var MediaFolder $johnDocuments */
        $johnDocuments = $this->getReference('MediaFolder-john-user-documents', MediaFolder::class);
        /** @var MediaFolder $johnContracts */
        $johnContracts = $this->getReference('MediaFolder-john-user-contracts', MediaFolder::class);

        /** @var MediaFolder $aliceImages */
        $aliceImages = $this->getReference('MediaFolder-alice-user-images', MediaFolder::class);
        /** @var MediaFolder $aliceDocuments */
        $aliceDocuments = $this->getReference('MediaFolder-alice-user-documents', MediaFolder::class);

        $ownerOkMedia = $this->createMedia(
            manager: $manager,
            reference: 'Media-john-user-avatar-png',
            uuid: '81000000-0000-1000-8000-000000000001',
            owner: $john,
            folder: $johnImages,
            name: 'avatar-john.png',
            path: 'media/john-user/images/avatar-john.png',
            mimeType: 'image/png',
            size: 245760,
        );

        $this->createMedia(
            manager: $manager,
            reference: 'Media-john-user-cover-jpg',
            uuid: '81000000-0000-1000-8000-000000000002',
            owner: $john,
            folder: $johnImages,
            name: 'cover-john.jpg',
            path: 'media/john-user/images/cover-john.jpg',
            mimeType: 'image/jpeg',
            size: 532480,
        );

        $this->createMedia(
            manager: $manager,
            reference: 'Media-john-user-cv-pdf',
            uuid: '81000000-0000-1000-8000-000000000003',
            owner: $john,
            folder: $johnDocuments,
            name: 'cv-john.pdf',
            path: 'media/john-user/documents/cv-john.pdf',
            mimeType: 'application/pdf',
            size: 870400,
        );

        $this->createMedia(
            manager: $manager,
            reference: 'Media-john-user-budget-xlsx',
            uuid: '81000000-0000-1000-8000-000000000004',
            owner: $john,
            folder: $johnDocuments,
            name: 'budget-2026.xlsx',
            path: 'media/john-user/documents/budget-2026.xlsx',
            mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            size: 1245184,
        );

        $this->createMedia(
            manager: $manager,
            reference: 'Media-john-user-contract-pptx',
            uuid: '81000000-0000-1000-8000-000000000005',
            owner: $john,
            folder: $johnContracts,
            name: 'contract-review.pptx',
            path: 'media/john-user/contracts/contract-review.pptx',
            mimeType: 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            size: 2187264,
        );

        $forbiddenMedia = $this->createMedia(
            manager: $manager,
            reference: 'Media-alice-user-contract-pdf',
            uuid: '81000000-0000-1000-8000-000000000006',
            owner: $alice,
            folder: $aliceDocuments,
            name: 'contrat-alice.pdf',
            path: 'media/alice-user/documents/contrat-alice.pdf',
            mimeType: 'application/pdf',
            size: 413696,
        );

        $this->createMedia(
            manager: $manager,
            reference: 'Media-alice-user-hero-jpg',
            uuid: '81000000-0000-1000-8000-000000000007',
            owner: $alice,
            folder: $aliceImages,
            name: 'hero-banner.jpg',
            path: 'media/alice-user/images/hero-banner.jpg',
            mimeType: 'image/jpeg',
            size: 720896,
        );

        $manager->flush();

        $this->addReference('Media-owner-ok', $ownerOkMedia);
        $this->addReference('Media-forbidden-cross-user', $forbiddenMedia);
    }

    #[Override]
    public function getOrder(): int
    {
        return 7;
    }

    private function createMedia(
        ObjectManager $manager,
        string $reference,
        string $uuid,
        User $owner,
        MediaFolder $folder,
        string $name,
        string $path,
        string $mimeType,
        int $size,
    ): Media {
        $media = (new Media())
            ->setOwner($owner)
            ->setFolder($folder)
            ->setName($name)
            ->setPath($path)
            ->setMimeType($mimeType)
            ->setSize($size)
            ->setStatus(MediaStatus::ACTIVE);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $media);

        $manager->persist($media);
        $this->addReference($reference, $media);

        return $media;
    }
}
