<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Domain\Entity;

use App\Configuration\Domain\Entity\Configuration;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'user_application')]
#[ORM\UniqueConstraint(name: 'uq_user_application_key_name', columns: ['key_name'])]
#[ORM\Index(name: 'idx_user_application_user_id', columns: ['user_id'])]
#[ORM\Index(name: 'idx_user_application_application_id', columns: ['application_id'])]
#[ORM\Index(name: 'idx_user_application_active', columns: ['active'])]
class UserApplication implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userApplications')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Application::class)]
    #[ORM\JoinColumn(name: 'application_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Application $application;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    private string $name = '';

    #[ORM\Column(name: 'logo', type: Types::STRING, length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'key_name', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private string $keyName = '';

    #[ORM\Column(name: 'is_public', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    private bool $public = false;

    #[ORM\Column(name: 'active', type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    private bool $active = true;

    /**
     * @var Collection<int, Configuration>
     */
    #[ORM\OneToMany(mappedBy: 'userApplication', targetEntity: Configuration::class)]
    private Collection $configurations;

    public function __construct(User $user, Application $application)
    {
        $this->id = $this->createUuid();
        $this->user = $user;
        $this->application = $application;
        $this->name = $application->getName();
        $this->logo = $application->getLogo();
        $this->description = $application->getDescription();
        $this->configurations = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Configuration>
     */
    public function getConfigurations(): Collection
    {
        return $this->configurations;
    }

    public function addConfiguration(Configuration $configuration): self
    {
        if (!$this->configurations->contains($configuration)) {
            $this->configurations->add($configuration);
            $configuration->setUserApplication($this);
        }

        return $this;
    }

    public function removeConfiguration(Configuration $configuration): self
    {
        if ($this->configurations->removeElement($configuration) && $configuration->getUserApplication() === $this) {
            $configuration->setUserApplication(null);
        }

        return $this;
    }
}
