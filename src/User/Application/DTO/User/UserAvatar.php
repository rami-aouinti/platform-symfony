<?php

declare(strict_types=1);

namespace App\User\Application\DTO\User;

use App\User\Domain\Entity\UserAvatar as Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\User
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class UserAvatar
{
    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/^[A-Za-z0-9][A-Za-z0-9_\-:.]{2,254}$/')]
    protected ?string $mediaId = null;

    #[Assert\NotBlank]
    #[Assert\Url]
    #[Assert\Length(max: 2048)]
    protected string $url = '';

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

    public static function fromEntity(Entity $entity): self
    {
        return (new self())
            ->setMediaId($entity->getMediaId())
            ->setUrl($entity->getUrl());
    }
}
