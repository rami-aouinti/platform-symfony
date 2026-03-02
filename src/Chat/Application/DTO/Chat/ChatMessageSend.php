<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ChatMessageSend
{
    #[Assert\Length(max: 10000)]
    private string $content = '';

    /**
     * @var array<int, array<string, mixed>>
     */
    #[Assert\Count(max: 10)]
    private array $attachments = [];

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param array<int, array<string, mixed>> $attachments
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    #[Assert\Callback]
    public function validatePayload(ExecutionContextInterface $context): void
    {
        if (trim($this->content) === '' && $this->attachments === []) {
            $context->buildViolation('Either content or at least one attachment is required.')
                ->atPath('content')
                ->addViolation();
        }
    }
}
