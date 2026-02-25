<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Resource;

use App\Company\Application\Resource\CompanyResource;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Message\CompanyCreatedMessage;
use App\Company\Domain\Repository\Interfaces\CompanyMembershipRepositoryInterface;
use App\Company\Domain\Repository\Interfaces\CompanyRepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Throwable;

class CompanyResourceTest extends TestCase
{
    /**
     * @throws Throwable
     */
    #[AllowMockObjectsWithoutExpectations]
    public function testAfterCreateDispatchesCompanyCreatedMessage(): void
    {
        $owner = (new User())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setUsername('john.doe')
            ->setEmail('john@example.com');

        $company = (new Company())
            ->setLegalName('Acme')
            ->setSlug('acme');

        $userTypeIdentification = $this->createMock(UserTypeIdentification::class);
        $userTypeIdentification
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($owner);

        $messageService = $this->createMock(MessageServiceInterface::class);
        $messageService
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(static function (object $message) use ($company, $owner): bool {
                if (!$message instanceof CompanyCreatedMessage) {
                    return false;
                }

                return $message->companyId === $company->getId()
                    && $message->ownerUserId === $owner->getId()
                    && $message->metadata === [
                        'legalName' => 'Acme',
                        'slug' => 'acme',
                    ];
            }))
            ->willReturnSelf();

        $resource = new CompanyResource(
            $this->createMock(CompanyRepositoryInterface::class),
            $userTypeIdentification,
            $this->createMock(CompanyMembershipRepositoryInterface::class),
            $messageService,
        );

        $resource->afterCreate($this->createMock(RestDtoInterface::class), $company);
    }
}
