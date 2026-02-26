<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Enum\SocialProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;

/**
 * @package App\User
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'user_social_account')]
#[ORM\UniqueConstraint(name: 'uq_social_provider_user', columns: ['provider', 'provider_user_id'])]
#[ORM\UniqueConstraint(name: 'uq_user_provider', columns: ['user_id', 'provider'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class SocialAccount implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'socialAccounts')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(name: 'provider', enumType: SocialProvider::class, type: Types::STRING, length: 32, nullable: false)]
    private SocialProvider $provider;

    #[ORM\Column(name: 'provider_user_id', type: Types::STRING, length: 255, nullable: false)]
    private string $providerUserId = '';

    #[ORM\Column(name: 'provider_email', type: Types::STRING, length: 255, nullable: true)]
    private ?string $providerEmail = null;

    public function __construct(User $user, SocialProvider $provider, string $providerUserId)
    {
        $this->id = $this->createUuid();
        $this->user = $user;
        $this->provider = $provider;
        $this->providerUserId = $providerUserId;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProvider(): SocialProvider
    {
        return $this->provider;
    }

    public function setProvider(SocialProvider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProviderUserId(): string
    {
        return $this->providerUserId;
    }

    public function setProviderUserId(string $providerUserId): self
    {
        $this->providerUserId = $providerUserId;

        return $this;
    }

    public function getProviderEmail(): ?string
    {
        return $this->providerEmail;
    }

    public function setProviderEmail(?string $providerEmail): self
    {
        $this->providerEmail = $providerEmail;

        return $this;
    }
}
