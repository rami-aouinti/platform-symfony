<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\JobApplication\Domain\Enum\ApplicationStatus;
use App\Offer\Domain\Entity\Offer;
use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'job_application')]
#[ORM\UniqueConstraint(name: 'uq_job_application_user_offer', columns: ['user_id', 'offer_id'])]
#[ORM\Index(name: 'idx_job_application_offer_status', columns: ['offer_id', 'status'])]
#[ORM\Index(name: 'idx_job_application_user_status', columns: ['user_id', 'status'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class JobApplication implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['JobApplication', 'JobApplication.id', 'JobApplication.show'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Offer::class)]
    #[ORM\JoinColumn(name: 'offer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['JobApplication', 'JobApplication.offer', 'JobApplication.show'])]
    private Offer $offer;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['JobApplication', 'JobApplication.user', 'JobApplication.show'])]
    private User $user;

    #[ORM\Column(name: 'status', type: 'string', enumType: ApplicationStatus::class, length: 32, nullable: false)]
    #[Groups(['JobApplication', 'JobApplication.status', 'JobApplication.show'])]
    private ApplicationStatus $status = ApplicationStatus::PENDING;

    public function __construct(Offer $offer, User $user)
    {
        $this->id = $this->createUuid();
        $this->offer = $offer;
        $this->user = $user;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getOffer(): Offer
    {
        return $this->offer;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStatus(): ApplicationStatus
    {
        return $this->status;
    }

    public function setStatus(ApplicationStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
