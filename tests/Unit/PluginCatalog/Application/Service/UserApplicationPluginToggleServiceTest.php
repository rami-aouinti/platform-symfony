<?php

declare(strict_types=1);

namespace App\Tests\Unit\PluginCatalog\Application\Service;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\PluginCatalog\Application\Service\UserApplicationPluginToggleService;
use App\PluginCatalog\Domain\Entity\Plugin;
use App\PluginCatalog\Domain\Entity\UserApplicationPlugin;
use App\PluginCatalog\Domain\Repository\Interfaces\UserApplicationPluginRepositoryInterface;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserApplicationPluginToggleServiceTest extends TestCase
{
    private UserApplicationPluginRepositoryInterface&MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserApplicationPluginRepositoryInterface::class);
    }

    public function testAttachCreatesPluginActivationWhenMissing(): void
    {
        $service = new UserApplicationPluginToggleService($this->repository);
        $plugin = (new Plugin())->setName('Chat')->setKeyName('chat');
        $userApplication = $this->createUserApplication();

        $this->repository
            ->expects(self::once())
            ->method('findOneByUserApplicationAndPlugin')
            ->with($userApplication, $plugin)
            ->willReturn(null);

        $this->repository
            ->expects(self::once())
            ->method('save')
            ->with(self::isInstanceOf(UserApplicationPlugin::class));

        $result = $service->attach($userApplication, $plugin);

        self::assertTrue($result->isActive());
        self::assertSame($userApplication, $result->getUserApplication());
        self::assertSame($plugin, $result->getPlugin());
    }

    public function testDetachRemovesExistingActivation(): void
    {
        $service = new UserApplicationPluginToggleService($this->repository);
        $plugin = (new Plugin())->setName('Chat')->setKeyName('chat');
        $userApplication = $this->createUserApplication();
        $existing = new UserApplicationPlugin($userApplication, $plugin);

        $this->repository
            ->expects(self::once())
            ->method('findOneByUserApplicationAndPlugin')
            ->with($userApplication, $plugin)
            ->willReturn($existing);

        $this->repository
            ->expects(self::once())
            ->method('remove')
            ->with($existing);

        $service->detach($userApplication, $plugin);
    }

    public function testDetachDoesNothingWhenActivationIsMissing(): void
    {
        $service = new UserApplicationPluginToggleService($this->repository);
        $plugin = (new Plugin())->setName('Chat')->setKeyName('chat');
        $userApplication = $this->createUserApplication();

        $this->repository
            ->expects(self::once())
            ->method('findOneByUserApplicationAndPlugin')
            ->with($userApplication, $plugin)
            ->willReturn(null);

        $this->repository
            ->expects(self::never())
            ->method('remove');

        $service->detach($userApplication, $plugin);
    }

    private function createUserApplication(): UserApplication
    {
        $user = new User();
        $application = (new Application())->setName('CRM');

        return new UserApplication($user, $application);
    }
}
