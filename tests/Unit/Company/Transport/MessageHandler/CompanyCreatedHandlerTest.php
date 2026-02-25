<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Transport\MessageHandler;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Message\CompanyCreatedMessage;
use App\Company\Infrastructure\Repository\CompanyRepository;
use App\Company\Transport\MessageHandler\CompanyCreatedHandler;
use App\Notification\Application\Service\Interfaces\NotificationOrchestratorInterface;
use App\User\Domain\Entity\User;
use App\User\Infrastructure\Repository\UserRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

class CompanyCreatedHandlerTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testInvokeDelegatesToNotificationOrchestrator(): void
    {
        $owner = (new User())
            ->setFirstName('Alice')
            ->setLastName('Owner')
            ->setUsername('alice.owner')
            ->setEmail('alice@example.com');

        $company = (new Company())
            ->setLegalName('Acme Corp')
            ->setSlug('acme-corp');

        $message = new CompanyCreatedMessage(
            companyId: $company->getId(),
            ownerUserId: $owner->getId(),
            metadata: [
                'legalName' => 'Acme Corp',
                'slug' => 'acme-corp',
            ],
        );

        $notificationOrchestrator = $this->createMock(NotificationOrchestratorInterface::class);
        $notificationOrchestrator
            ->expects($this->once())
            ->method('notifyCompanyCreated')
            ->with(
                $owner,
                'Acme Corp',
            );

        $companyRepository = $this->getMockBuilder(CompanyRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $companyRepository
            ->expects($this->once())
            ->method('find')
            ->with($company->getId())
            ->willReturn($company);

        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();
        $userRepository
            ->expects($this->once())
            ->method('find')
            ->with($owner->getId())
            ->willReturn($owner);

        $handler = new CompanyCreatedHandler(
            $notificationOrchestrator,
            $companyRepository,
            $userRepository,
        );

        $handler($message);
    }
}
