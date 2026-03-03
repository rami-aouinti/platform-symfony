<?php

declare(strict_types=1);

namespace App\PluginCatalog\Domain\Entity;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'user_application_plugin')]
#[ORM\UniqueConstraint(name: 'uq_user_application_plugin_ua_plugin', columns: ['user_application_id', 'plugin_id'])]
#[ORM\Index(name: 'idx_user_application_plugin_user_application_id', columns: ['user_application_id'])]
#[ORM\Index(name: 'idx_user_application_plugin_plugin_id', columns: ['plugin_id'])]
#[ORM\Index(name: 'idx_user_application_plugin_active', columns: ['active'])]
class UserApplicationPlugin implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: UserApplication::class)]
    #[ORM\JoinColumn(name: 'user_application_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UserApplication $userApplication;

    #[ORM\ManyToOne(targetEntity: Plugin::class)]
    #[ORM\JoinColumn(name: 'plugin_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Plugin $plugin;

    #[ORM\Column(name: 'active', type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    private bool $active = true;

    public function __construct(UserApplication $userApplication, Plugin $plugin)
    {
        $this->id = $this->createUuid();
        $this->userApplication = $userApplication;
        $this->plugin = $plugin;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUserApplication(): UserApplication
    {
        return $this->userApplication;
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
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
