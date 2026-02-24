<?php

declare(strict_types=1);

namespace App\Notification\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'notification')]
#[ORM\Index(name: 'idx_notification_user_read_at', columns: ['user_id', 'read_at'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Notification implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Notification', 'Notification.id'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Notification', 'Notification.title'])]
    private string $title = '';

    #[ORM\Column(name: 'message', type: Types::TEXT, nullable: false)]
    #[Groups(['Notification', 'Notification.message'])]
    private string $message = '';

    #[ORM\Column(name: 'type', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['Notification', 'Notification.type'])]
    private string $type = 'info';

    #[ORM\Column(name: 'read_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['Notification', 'Notification.readAt'])]
    private ?\DateTimeInterface $readAt = null;

    public function __construct(User $user)
    {
        $this->id = $this->createUuid();
        $this->user = $user;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getReadAt(): ?\DateTimeInterface
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTimeInterface $readAt): self
    {
        $this->readAt = $readAt;

        return $this;
    }

    public function isRead(): bool
    {
        return $this->readAt !== null;
    }
}
