<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'user_application')]
#[ORM\UniqueConstraint(name: 'uq_user_application_user_application', columns: ['user_id', 'application_id'])]
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

    #[ORM\Column(name: 'active', type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    private bool $active = true;

    public function __construct(User $user, Application $application)
    {
        $this->id = $this->createUuid();
        $this->user = $user;
        $this->application = $application;
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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
