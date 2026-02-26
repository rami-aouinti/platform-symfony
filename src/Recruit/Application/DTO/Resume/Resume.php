<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\Resume;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Resume as Entity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class Resume extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    protected string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 10000)]
    protected string $summary = '';

    /**
     * @var array<int, array<string, mixed>>
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Collection([
            'fields' => [
                'company' => new Assert\Required([new Assert\Type('string'), new Assert\Length(max: 255)]),
                'position' => new Assert\Required([new Assert\Type('string'), new Assert\Length(max: 255)]),
                'startDate' => new Assert\Optional([new Assert\Date()]),
                'endDate' => new Assert\Optional([new Assert\Date()]),
                'description' => new Assert\Optional([new Assert\Type('string')]),
            ],
            'allowMissingFields' => true,
            'allowExtraFields' => true,
        ]),
    ])]
    protected array $experiences = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Collection([
            'fields' => [
                'institution' => new Assert\Required([new Assert\Type('string'), new Assert\Length(max: 255)]),
                'degree' => new Assert\Optional([new Assert\Type('string'), new Assert\Length(max: 255)]),
                'startDate' => new Assert\Optional([new Assert\Date()]),
                'endDate' => new Assert\Optional([new Assert\Date()]),
            ],
            'allowMissingFields' => true,
            'allowExtraFields' => true,
        ]),
    ])]
    protected array $education = [];

    /**
     * @var array<int, string>
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('string'),
        new Assert\Length(min: 1, max: 128),
    ])]
    protected array $skills = [];

    /**
     * @var array<int, array<string, string>>
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Collection([
            'fields' => [
                'label' => new Assert\Required([new Assert\Type('string'), new Assert\Length(max: 255)]),
                'url' => new Assert\Required([new Assert\Type('string'), new Assert\Url(), new Assert\Length(max: 2048)]),
            ],
            'allowMissingFields' => false,
            'allowExtraFields' => false,
        ]),
    ])]
    protected array $links = [];

    #[Assert\Type('bool')]
    protected bool $isPublic = false;

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

    public function getSummary(): string
    {
        return $this->summary;
    }
    public function setSummary(string $summary): self
    {
        $this->setVisited('summary');
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getExperiences(): array
    {
        return $this->experiences;
    }

    /**
     * @param array<int, array<string, mixed>> $experiences
     */
    public function setExperiences(array $experiences): self
    {
        $this->setVisited('experiences');
        $this->experiences = $experiences;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getEducation(): array
    {
        return $this->education;
    }

    /**
     * @param array<int, array<string, mixed>> $education
     */
    public function setEducation(array $education): self
    {
        $this->setVisited('education');
        $this->education = $education;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @param array<int, string> $skills
     */
    public function setSkills(array $skills): self
    {
        $this->setVisited('skills');
        $this->skills = $skills;

        return $this;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array<int, array<string, string>> $links
     */
    public function setLinks(array $links): self
    {
        $this->setVisited('links');
        $this->links = $links;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }
    public function setIsPublic(bool $isPublic): self
    {
        $this->setVisited('isPublic');
        $this->isPublic = $isPublic;

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
            $this->title = $entity->getTitle();
            $this->summary = $entity->getSummary();
            $this->experiences = $entity->getExperiences();
            $this->education = $entity->getEducation();
            $this->skills = $entity->getSkills();
            $this->links = $entity->getLinks();
            $this->isPublic = $entity->isPublic();
        }

        return $this;
    }
}
