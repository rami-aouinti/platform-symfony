<?php

declare(strict_types=1);

namespace App\Tests\Unit\ApplicationCatalog\Application\Service;

use App\ApplicationCatalog\Application\Service\UserApplicationToggleService;
use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\ApplicationCatalog\Domain\Repository\Interfaces\UserApplicationRepositoryInterface;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserApplicationToggleServiceTest extends TestCase
{
    private UserApplicationRepositoryInterface&MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserApplicationRepositoryInterface::class);
    }

    public function testToggleCreatesUserApplicationWhenMissing(): void
    {
        $service = new UserApplicationToggleService($this->repository);
        $user = new User();
        $application = (new Application())->setName('CRM');

        $this->repository
            ->expects(self::once())
            ->method('findOneByUserAndApplication')
            ->with($user, $application)
            ->willReturn(null);

        $this->repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(static function (object $entity) use ($user, $application): bool {
                if (!$entity instanceof UserApplication) {
                    return false;
                }

                return $entity->getUser() === $user
                    && $entity->getApplication() === $application
                    && $entity->isActive();
            }));

        $result = $service->toggle($user, $application, true);

        self::assertInstanceOf(UserApplication::class, $result);
        self::assertTrue($result->isActive());
    }

    public function testToggleUpdatesExistingState(): void
    {
        $service = new UserApplicationToggleService($this->repository);
        $user = new User();
        $application = (new Application())->setName('CRM');
        $existing = (new UserApplication($user, $application))->setActive(true);

        $this->repository
            ->expects(self::once())
            ->method('findOneByUserAndApplication')
            ->with($user, $application)
            ->willReturn($existing);

        $this->repository
            ->expects(self::once())
            ->method('save')
            ->with($existing);

        $result = $service->toggle($user, $application, false);

        self::assertSame($existing, $result);
        self::assertFalse($result->isActive());
    }
}
