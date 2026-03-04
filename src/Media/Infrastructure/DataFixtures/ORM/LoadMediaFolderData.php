<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Media\Domain\Entity\MediaFolder;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\Media\Infrastructure\DataFixtures\ORM
 */
final class LoadMediaFolderData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $johnRoot */
        $johnRoot = $this->getReference('User-john-root', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);

        $johnRootFolder = $this->createFolder($manager, 'MediaFolder-john-root-root', '80000000-0000-1000-8000-000000000001', MediaFolder::ROOT_FOLDER_NAME, $johnRoot, null);
        $aliceRoot = $this->createFolder($manager, 'MediaFolder-alice-user-root', '80000000-0000-1000-8000-000000000002', MediaFolder::ROOT_FOLDER_NAME, $alice, null);

        $johnImages = $this->createFolder($manager, 'MediaFolder-john-root-images', '80000000-0000-1000-8000-000000000011', 'Images', $johnRoot, $johnRootFolder);
        $johnDocuments = $this->createFolder($manager, 'MediaFolder-john-root-documents', '80000000-0000-1000-8000-000000000012', 'Documents', $johnRoot, $johnRootFolder);
        $johnContracts = $this->createFolder($manager, 'MediaFolder-john-root-contracts', '80000000-0000-1000-8000-000000000013', 'Contrats', $johnRoot, $johnRootFolder);
        $johnArchives = $this->createFolder($manager, 'MediaFolder-john-root-archives', '80000000-0000-1000-8000-000000000014', 'Archives', $johnRoot, $johnRootFolder);
        $this->createFolder($manager, 'MediaFolder-john-root-shared', '80000000-0000-1000-8000-000000000015', 'Shared', $johnRoot, $johnRootFolder);

        $this->createFolder($manager, 'MediaFolder-john-root-images-events', '80000000-0000-1000-8000-000000000016', 'Events', $johnRoot, $johnImages);
        $this->createFolder($manager, 'MediaFolder-john-root-documents-reports', '80000000-0000-1000-8000-000000000017', 'Reports', $johnRoot, $johnDocuments);
        $this->createFolder($manager, 'MediaFolder-john-root-contracts-signed', '80000000-0000-1000-8000-000000000018', 'Signed', $johnRoot, $johnContracts);
        $this->createFolder($manager, 'MediaFolder-john-root-archives-2024', '80000000-0000-1000-8000-000000000019', '2024', $johnRoot, $johnArchives);

        $this->createFolder($manager, 'MediaFolder-alice-user-images', '80000000-0000-1000-8000-000000000021', 'Images', $alice, $aliceRoot);
        $this->createFolder($manager, 'MediaFolder-alice-user-documents', '80000000-0000-1000-8000-000000000022', 'Documents', $alice, $aliceRoot);
        $this->createFolder($manager, 'MediaFolder-alice-user-contracts', '80000000-0000-1000-8000-000000000023', 'Contrats', $alice, $aliceRoot);

        $manager->flush();

        $this->addReference('MediaFolder-owner-ok', $johnRootFolder);
        $this->addReference('MediaFolder-forbidden-cross-user', $aliceRoot);
    }

    #[Override]
    public function getOrder(): int
    {
        return 6;
    }

    private function createFolder(
        ObjectManager $manager,
        string $reference,
        string $uuid,
        string $name,
        User $owner,
        ?MediaFolder $parent,
    ): MediaFolder {
        $folder = (new MediaFolder())
            ->setName($name)
            ->setOwner($owner)
            ->setParent($parent);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $folder);

        $manager->persist($folder);
        $this->addReference($reference, $folder);

        return $folder;
    }
}
