<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\User\Domain\Entity
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'user_avatar')]
#[ORM\UniqueConstraint(name: 'uq_user_avatar_user_profile_id', columns: ['user_profile_id'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class UserAvatar implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['UserProfile.avatar', 'User.userProfile'])]
    private UuidInterface $id;

    #[ORM\OneToOne(targetEntity: UserProfile::class, inversedBy: 'avatar')]
    #[ORM\JoinColumn(name: 'user_profile_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UserProfile $userProfile;

    #[ORM\Column(name: 'media_id', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['UserProfile.avatar', 'User.userProfile'])]
    private ?string $mediaId = null;

    #[ORM\Column(name: 'url', type: Types::STRING, length: 2048, nullable: false)]
    #[Groups(['UserProfile.avatar', 'User.userProfile'])]
    private string $url = '';

    public function __construct(UserProfile $userProfile)
    {
        $this->id = $this->createUuid();
        $this->userProfile = $userProfile;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUserProfile(): UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(UserProfile $userProfile): self
    {
        $this->userProfile = $userProfile;

        if ($userProfile->getAvatar() !== $this) {
            $userProfile->setAvatar($this);
        }

        return $this;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function setMediaId(?string $mediaId): self
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
