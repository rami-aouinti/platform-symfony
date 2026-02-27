<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\DescriptionTrait;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Recruit\Domain\Enum\OfferStatus;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\Offer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'offer')]
#[ORM\Index(name: 'idx_offer_company_status', columns: ['company_id', 'status'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Offer implements EntityInterface
{
    use DescriptionTrait;
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Offer', 'Offer.id', 'Offer.show', 'Offer.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Offer', 'Offer.title', 'Offer.create', 'Offer.show', 'Offer.edit'])]
    private string $title = '';

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Offer', 'Offer.company', 'Offer.create', 'Offer.show', 'Offer.edit'])]
    private ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Offer', 'Offer.createdBy', 'Offer.show'])]
    private ?User $createdBy = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, enumType: OfferStatus::class)]
    #[Groups(['Offer', 'Offer.status', 'Offer.create', 'Offer.show', 'Offer.edit'])]
    private OfferStatus $status = OfferStatus::DRAFT;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getStatus(): OfferStatus
    {
        return $this->status;
    }

    public function setStatus(OfferStatus|string $status): self
    {
        $nextStatus = $status instanceof OfferStatus ? $status : OfferStatus::from($status);

        if (!$this->status->canTransitionTo($nextStatus) && $this->status !== $nextStatus) {
            return $this;
        }

        $this->status = $nextStatus;

        return $this;
    }
}
