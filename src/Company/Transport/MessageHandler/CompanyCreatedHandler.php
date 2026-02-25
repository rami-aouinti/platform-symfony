<?php

declare(strict_types=1);

namespace App\Company\Transport\MessageHandler;

use App\Company\Infrastructure\Repository\CompanyRepository;
use App\Company\Domain\Message\CompanyCreatedMessage;
use App\Notification\Application\Service\Interfaces\NotificationChannelServiceInterface;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;

use function sprintf;

#[AsMessageHandler]
readonly class CompanyCreatedHandler
{
    public function __construct(
        private NotificationChannelServiceInterface $notificationChannelService,
        private CompanyRepository $companyRepository,
        private UserRepository $userRepository,
        private Environment $twig,
    ) {
    }

    public function __invoke(CompanyCreatedMessage $message): void
    {
        $owner = $this->userRepository->find($message->ownerUserId);
        $company = $this->companyRepository->find($message->companyId);

        if ($owner === null || $company === null) {
            return;
        }

        $subject = sprintf('Société "%s" créée', $company->getLegalName());
        $content = $this->twig->render('Emails/company_created.html.twig', [
            'owner' => $owner,
            'company' => $company,
            'metadata' => $message->metadata,
        ]);

        $this->notificationChannelService->sendEmailNotification($owner->getEmail(), $subject, $content);
    }
}
