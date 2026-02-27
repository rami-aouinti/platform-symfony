<?php

declare(strict_types=1);

namespace App\Media\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\NameTrait;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Media\Domain\Enum\MediaStatus;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\Media
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'media')]
#[ORM\Index(name: 'idx_media_owner_id', columns: ['owner_id'])]
#[ORM\Index(name: 'idx_media_status', columns: ['status'])]
#[ORM\Index(name: 'idx_media_mime_type', columns: ['mime_type'])]
#[ORM\UniqueConstraint(name: 'uq_media_owner_path', columns: ['owner_id', 'path'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Media implements EntityInterface
{
    use NameTrait;
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Media', 'Media.id', 'Media.show', 'Media.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Media', 'Media.owner', 'Media.show', 'Media.edit'])]
    private ?User $owner = null;

    #[ORM\Column(name: 'path', type: Types::STRING, length: 512, nullable: false)]
    #[Groups(['Media', 'Media.path', 'Media.create', 'Media.show', 'Media.edit'])]
    private string $path = '';

    #[ORM\Column(name: 'mime_type', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Media', 'Media.mimeType', 'Media.create', 'Media.show', 'Media.edit'])]
    private string $mimeType = '';

    #[ORM\Column(name: 'size', type: Types::INTEGER, nullable: false)]
    #[Groups(['Media', 'Media.size', 'Media.create', 'Media.show', 'Media.edit'])]
    private int $size = 0;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, enumType: MediaStatus::class)]
    #[Groups(['Media', 'Media.status', 'Media.create', 'Media.show', 'Media.edit'])]
    private MediaStatus $status = MediaStatus::ACTIVE;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getStatus(): MediaStatus
    {
        return $this->status;
    }

    public function setStatus(MediaStatus|string $status): self
    {
        $nextStatus = $status instanceof MediaStatus ? $status : MediaStatus::from($status);

        if (!$this->status->canTransitionTo($nextStatus) && $this->status !== $nextStatus) {
            return $this;
        }

        $this->status = $nextStatus;

        return $this;
    }
}
