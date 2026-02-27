<?php

declare(strict_types=1);

namespace App\Configuration\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
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
#[ORM\UniqueConstraint(name: 'uq_configuration_key', columns: ['key_name'])]
#[ORM\Index(name: 'idx_configuration_status', columns: ['status'])]
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

    #[ORM\Column(name: 'value', type: Types::TEXT, nullable: false)]
    #[Groups(['Configuration', 'Configuration.value', 'Configuration.create', 'Configuration.show', 'Configuration.edit'])]
    private string $value = '';

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

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
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
}
