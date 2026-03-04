<?php

declare(strict_types=1);

namespace App\Media\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\NameTrait;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;

/**
 * @package App\Media\Domain\Entity
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'media_folder')]
#[ORM\Index(name: 'idx_media_folder_owner_id', columns: ['owner_id'])]
#[ORM\Index(name: 'idx_media_folder_parent_id', columns: ['parent_id'])]
#[ORM\UniqueConstraint(name: 'uq_media_folder_owner_parent_name', columns: ['owner_id', 'parent_id', 'name'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class MediaFolder implements EntityInterface
{
    use NameTrait;
    use Timestampable;
    use Uuid;

    public const string ROOT_FOLDER_NAME = 'root';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?self $parent = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
