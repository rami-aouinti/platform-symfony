<?php

declare(strict_types=1);

namespace App\Configuration\Application\DTO\Configuration;

use App\Configuration\Domain\Entity\Configuration as Entity;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 * @package App\Configuration\Application\DTO\Configuration
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class Configuration extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $code = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $keyName = '';

    #[Assert\NotNull]
    #[Assert\Length(max: 65535)]
    protected string $value = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 64)]
    protected string $status = 'active';

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->setVisited('code');
        $this->code = $code;

        return $this;
    }

    public function getKeyName(): string
    {
        return $this->keyName;
    }

    public function setKeyName(string $keyName): self
    {
        $this->setVisited('keyName');
        $this->keyName = $keyName;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->setVisited('value');
        $this->value = $value;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->setVisited('status');
        $this->status = $status;

        return $this;
    }

    /**
     * @param EntityInterface|Entity $entity
     */
    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->code = $entity->getCode();
            $this->keyName = $entity->getKeyName();
            $this->value = $entity->getValue();
            $this->status = $entity->getStatus();
        }

        return $this;
    }
}
