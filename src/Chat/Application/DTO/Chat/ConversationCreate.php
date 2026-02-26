<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\General\Application\Validator\Constraints as AppAssert;
use App\JobApplication\Domain\Entity\JobApplication;
use Symfony\Component\Validator\Constraints as Assert;

class ConversationCreate
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(JobApplication::class)]
    private ?JobApplication $jobApplication = null;

    public function getJobApplication(): ?JobApplication
    {
        return $this->jobApplication;
    }

    public function setJobApplication(?JobApplication $jobApplication): self
    {
        $this->jobApplication = $jobApplication;

        return $this;
    }
}
