<?php

declare(strict_types=1);

namespace App\Page\Application\DTO\Contact;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Page\Domain\Entity\Contact as Entity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class Contact extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email]
    protected string $email = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $subject = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    protected string $content = '';

    #[Assert\Length(max: 255)]
    protected ?string $templateId = null;

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->setVisited('email'); $this->email = $email; return $this; }
    public function getSubject(): string { return $this->subject; }
    public function setSubject(string $subject): self { $this->setVisited('subject'); $this->subject = $subject; return $this; }
    public function getContent(): string { return $this->content; }
    public function setContent(string $content): self { $this->setVisited('content'); $this->content = $content; return $this; }
    public function getTemplateId(): ?string { return $this->templateId; }
    public function setTemplateId(?string $templateId): self { $this->setVisited('templateId'); $this->templateId = $templateId; return $this; }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->email = $entity->getEmail();
            $this->subject = $entity->getSubject();
            $this->content = $entity->getContent();
            $this->templateId = $entity->getTemplateId();
        }

        return $this;
    }
}
