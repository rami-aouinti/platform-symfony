<?php

declare(strict_types=1);

namespace App\Configuration\Domain\Entity;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\UserProfile;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Configuration.
 *
 * @package App\Configuration\Domain\Entity
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'configuration')]
#[ORM\UniqueConstraint(name: 'uq_configuration_user_application_code_key', columns: ['user_application_id', 'code', 'key_name'])]
#[ORM\Index(name: 'idx_configuration_status', columns: ['status'])]
#[ORM\Index(name: 'idx_configuration_profile', columns: ['profile_id'])]
#[ORM\Index(name: 'idx_configuration_user_application', columns: ['user_application_id'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Configuration implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Configuration', 'Configuration.id', 'Configuration.show', 'Configuration.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'code', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Configuration', 'Configuration.code', 'Configuration.create', 'Configuration.show', 'Configuration.edit'])]
    private string $code = '';

    #[ORM\Column(name: 'key_name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Configuration', 'Configuration.keyName', 'Configuration.create', 'Configuration.show', 'Configuration.edit'])]
    private string $keyName = '';

    #[ORM\Column(name: 'value', type: Types::JSON, nullable: false)]
    #[Groups(['Configuration', 'Configuration.value', 'Configuration.create', 'Configuration.show', 'Configuration.edit'])]
    private array $value = [];

    /**
     * Transitional scope: use either profile or userApplication, never both at once.
     */
    #[ORM\ManyToOne(targetEntity: UserProfile::class)]
    #[ORM\JoinColumn(name: 'profile_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?UserProfile $profile = null;

    #[ORM\ManyToOne(targetEntity: UserApplication::class, inversedBy: 'configurations')]
    #[ORM\JoinColumn(name: 'user_application_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?UserApplication $userApplication = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['Configuration', 'Configuration.status', 'Configuration.create', 'Configuration.show', 'Configuration.edit'])]
    private string $status = 'active';

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getKeyName(): string
    {
        return $this->keyName;
    }

    public function setKeyName(string $keyName): self
    {
        $this->keyName = $keyName;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @param array<mixed> $value
     */
    public function setValue(array $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    public function setProfile(?UserProfile $profile): self
    {
        $this->profile = $profile;

        if ($profile instanceof UserProfile) {
            $this->userApplication = null;
        }

        return $this;
    }

    public function getUserApplication(): ?UserApplication
    {
        return $this->userApplication;
    }

    public function setUserApplication(?UserApplication $userApplication): self
    {
        $this->userApplication = $userApplication;

        if ($userApplication instanceof UserApplication) {
            $this->profile = null;
        }

        return $this;
    }
}
