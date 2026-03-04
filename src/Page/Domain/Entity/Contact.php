<?php

declare(strict_types=1);

namespace App\Page\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'page_contact')]
#[ORM\Index(name: 'idx_page_contact_email', columns: ['email'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Contact implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Contact', 'Contact.id', 'Contact.show', 'Contact.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Contact', 'Contact.email', 'Contact.create', 'Contact.show', 'Contact.edit'])]
    private string $email = '';

    #[ORM\Column(name: 'subject', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Contact', 'Contact.subject', 'Contact.create', 'Contact.show', 'Contact.edit'])]
    private string $subject = '';

    #[ORM\Column(name: 'content', type: Types::TEXT, nullable: false)]
    #[Groups(['Contact', 'Contact.content', 'Contact.create', 'Contact.show', 'Contact.edit'])]
    private string $content = '';

    #[ORM\Column(name: 'template_id', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['Contact', 'Contact.templateId', 'Contact.create', 'Contact.show', 'Contact.edit'])]
    private ?string $templateId = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function setTemplateId(?string $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }
}
