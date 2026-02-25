<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Transport\MessageHandler;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Message\CompanyCreatedMessage;
use App\Company\Infrastructure\Repository\CompanyRepository;
use App\Company\Transport\MessageHandler\CompanyCreatedHandler;
use App\Notification\Application\Service\Interfaces\NotificationChannelServiceInterface;
use App\User\Domain\Entity\User;
use App\User\Infrastructure\Repository\UserRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class CompanyCreatedHandlerTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testInvokeSendsEmailNotificationWithTwigContent(): void
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

        $notificationChannelService = $this->createMock(NotificationChannelServiceInterface::class);
        $notificationChannelService
            ->expects($this->once())
            ->method('sendEmailNotification')
            ->with(
                'alice@example.com',
                'Société "Acme Corp" créée',
                '<p>Email body</p>'
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

        $twig = $this->createMock(Environment::class);
        $twig
            ->expects($this->once())
            ->method('render')
            ->with('Emails/company_created.html.twig', $this->callback(static function (array $context) use ($owner, $company): bool {
                return $context['owner'] === $owner
                    && $context['company'] === $company
                    && $context['metadata']['slug'] === 'acme-corp';
            }))
            ->willReturn('<p>Email body</p>');

        $handler = new CompanyCreatedHandler(
            $notificationChannelService,
            $companyRepository,
            $userRepository,
            $twig,
        );

        $handler($message);
    }
}
