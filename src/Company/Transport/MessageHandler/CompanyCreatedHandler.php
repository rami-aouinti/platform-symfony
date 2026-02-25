<?php

declare(strict_types=1);

namespace App\Company\Transport\MessageHandler;

use App\Company\Infrastructure\Repository\CompanyRepository;
use App\Company\Domain\Message\CompanyCreatedMessage;
use App\Notification\Application\Service\Interfaces\NotificationOrchestratorInterface;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CompanyCreatedHandler
{
    public function __construct(
        private NotificationOrchestratorInterface $notificationOrchestrator,
        private CompanyRepository $companyRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(CompanyCreatedMessage $message): void
    {
        $owner = $this->userRepository->find($message->ownerUserId);
        $company = $this->companyRepository->find($message->companyId);

        if ($owner === null || $company === null) {
            return;
        }

        $this->notificationOrchestrator->notifyCompanyCreated($owner, $company->getLegalName());
    }
}
