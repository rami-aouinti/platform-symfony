<?php

declare(strict_types=1);

namespace App\Friend\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'friend_request')]
#[ORM\UniqueConstraint(name: 'uq_friend_request_requester_addressee', columns: ['requester_id', 'addressee_id'])]
#[ORM\Index(name: 'idx_friend_request_addressee_status', columns: ['addressee_id', 'status'])]
#[ORM\Index(name: 'idx_friend_request_requester_status', columns: ['requester_id', 'status'])]
class FriendRequest implements EntityInterface
{
    use Timestampable;
    use Uuid;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'requester_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $requester;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'addressee_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $addressee;

    #[ORM\Column(name: 'status', type: 'string', length: 16, nullable: false)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(name: 'accepted_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $acceptedAt = null;

    public function __construct(User $requester, User $addressee)
    {
        $this->id = $this->createUuid();
        $this->requester = $requester;
        $this->addressee = $addressee;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getRequester(): User
    {
        return $this->requester;
    }

    public function getAddressee(): User
    {
        return $this->addressee;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function accept(): self
    {
        $this->status = self::STATUS_ACCEPTED;
        $this->acceptedAt = new \DateTimeImmutable();

        return $this;
    }
}
