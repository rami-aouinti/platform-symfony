<?php

declare(strict_types=1);

namespace App\Quiz\Application\DTO\Quiz;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Quiz\Domain\Entity\Quiz as Entity;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 * @package App\Quiz\Application\DTO\Quiz
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class Quiz extends RestDto
{
    private const CATEGORY_CHOICES = ['general', 'science', 'history', 'sports', 'technology', 'entertainment'];

    private const DIFFICULTY_CHOICES = ['easy', 'medium', 'hard'];

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $title = '';

    #[Assert\Length(max: 5000)]
    protected ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::CATEGORY_CHOICES)]
    protected string $category = 'general';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::DIFFICULTY_CHOICES)]
    protected string $difficulty = 'easy';

    #[Assert\Positive]
    protected ?int $timeLimit = null;

    protected bool $isPublished = false;

    protected ?DateTimeImmutable $startsAt = null;

    protected ?DateTimeImmutable $endsAt = null;

    #[AppAssert\EntityReferenceExists(User::class)]
    protected ?User $owner = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->setVisited('title');
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->setVisited('description');
        $this->description = $description;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->setVisited('category');
        $this->category = $category;

        return $this;
    }

    public function getDifficulty(): string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): self
    {
        $this->setVisited('difficulty');
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getTimeLimit(): ?int
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(?int $timeLimit): self
    {
        $this->setVisited('timeLimit');
        $this->timeLimit = $timeLimit;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->setVisited('isPublished');
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getStartsAt(): ?DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(?DateTimeImmutable $startsAt): self
    {
        $this->setVisited('startsAt');
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?DateTimeImmutable $endsAt): self
    {
        $this->setVisited('endsAt');
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->setVisited('owner');
        $this->owner = $owner;

        return $this;
    }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->title = $entity->getTitle();
            $this->description = $entity->getDescription();
            $this->category = $entity->getCategory();
            $this->difficulty = $entity->getDifficulty();
            $this->timeLimit = $entity->getTimeLimit();
            $this->isPublished = $entity->isPublished();
            $this->startsAt = $entity->getStartsAt();
            $this->endsAt = $entity->getEndsAt();
            $this->owner = $entity->getOwner();
        }

        return $this;
    }
}
