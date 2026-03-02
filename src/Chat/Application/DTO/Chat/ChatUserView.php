<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\User\Domain\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

class ChatUserView
{
    #[Groups(['default'])]
    private string $id;

    #[Groups(['default'])]
    private string $username;

    #[Groups(['default'])]
    private string $firstName;

    #[Groups(['default'])]
    private string $lastName;

    #[Groups(['default'])]
    private string $photo;

    #[Groups(['default'])]
    private bool $isCurrentUser;

    public function __construct(User $user, string $currentUserId)
    {
        $this->id = $user->getId();
        $this->username = $user->getUsername();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->photo = $user->getPhoto();
        $this->isCurrentUser = $user->getId() === $currentUserId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function isCurrentUser(): bool
    {
        return $this->isCurrentUser;
    }
}
