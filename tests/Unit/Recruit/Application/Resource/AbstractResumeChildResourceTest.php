<?php

declare(strict_types=1);

namespace App\Tests\Unit\Recruit\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use App\Recruit\Application\Resource\AbstractResumeChildResource;
use App\Recruit\Domain\Entity\Resume;
use App\Recruit\Domain\Entity\ResumeEducation;
use App\Recruit\Domain\Entity\ResumeExperience;
use App\Recruit\Domain\Entity\ResumeSkill;
use App\User\Application\Security\Permission;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AbstractResumeChildResourceTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: object}>
     */
    public static function provideHookMethodsWithDto(): array
    {
        $entities = [
            'experience' => new ResumeExperience(),
            'education' => new ResumeEducation(),
            'skill' => new ResumeSkill(),
        ];

        $cases = [];

        foreach (['beforeCreate', 'beforeUpdate', 'beforePatch'] as $method) {
            foreach ($entities as $name => $entity) {
                $cases[$method . '_' . $name] = [$method, clone $entity];
            }
        }

        return $cases;
    }

    /**
     * @return array<string, array{0: object}>
     */
    public static function provideResumeChildren(): array
    {
        return [
            'experience' => [new ResumeExperience()],
            'education' => [new ResumeEducation()],
            'skill' => [new ResumeSkill()],
        ];
    }

    #[AllowMockObjectsWithoutExpectations]
    #[DataProvider('provideHookMethodsWithDto')]
    public function testHooksWithDtoDenyAccessWhenResumeEditPermissionIsMissing(string $method, object $entity): void
    {
        $resume = new Resume();
        $entity->setResume($resume);

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with(Permission::RESUME_EDIT->value, $resume)
            ->willReturn(false);

        $resource = $this->createResource($authorizationChecker);

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Only resume owner can edit this resume.');

        if ('beforeCreate' === $method) {
            $resource->beforeCreate($this->createMock(RestDtoInterface::class), $entity);

            return;
        }

        $id = 'child-id';
        $resource->{$method}($id, $this->createMock(RestDtoInterface::class), $entity);
    }

    #[AllowMockObjectsWithoutExpectations]
    #[DataProvider('provideResumeChildren')]
    public function testBeforeDeleteDeniesAccessWhenResumeEditPermissionIsMissing(object $entity): void
    {
        $resume = new Resume();
        $entity->setResume($resume);

        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with(Permission::RESUME_EDIT->value, $resume)
            ->willReturn(false);

        $resource = $this->createResource($authorizationChecker);

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Only resume owner can edit this resume.');

        $id = 'child-id';
        $resource->beforeDelete($id, $entity);
    }

    private function createResource(AuthorizationCheckerInterface $authorizationChecker): AbstractResumeChildResource
    {
        return new class($authorizationChecker, $this->createMock(BaseRepositoryInterface::class)) extends AbstractResumeChildResource {
        };
    }
}
