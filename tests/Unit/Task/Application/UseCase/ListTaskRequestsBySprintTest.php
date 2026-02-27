<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Application\UseCase;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\ListTaskRequestsBySprint;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use App\User\Domain\Entity\User;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ListTaskRequestsBySprint::class)]
class ListTaskRequestsBySprintTest extends TestCase
{
    public function testNonAdminVisibilityIncludesReviewerToMatchTaskAccessRules(): void
    {
        [$useCase, $whereClauses] = $this->buildUseCaseWithQueryCapture(false, '11111111-1111-1111-1111-111111111111');

        $useCase->execute('22222222-2222-2222-2222-222222222222');

        self::assertContains(
            'requester.id = :currentUserId OR reviewer.id = :currentUserId OR taskOwner.id = :currentUserId OR projectOwner.id = :currentUserId',
            $whereClauses,
        );
    }

    public function testNonInvolvedUserRemainsRestrictedByNonAdminVisibilityClause(): void
    {
        [$useCase, $whereClauses] = $this->buildUseCaseWithQueryCapture(false, '33333333-3333-3333-3333-333333333333');

        $useCase->execute('44444444-4444-4444-4444-444444444444');

        self::assertContains(
            'requester.id = :currentUserId OR reviewer.id = :currentUserId OR taskOwner.id = :currentUserId OR projectOwner.id = :currentUserId',
            $whereClauses,
        );
    }

    public function testAdminKeepsFullVisibilityWithoutNonAdminRestrictionClause(): void
    {
        [$useCase, $whereClauses] = $this->buildUseCaseWithQueryCapture(true, '55555555-5555-5555-5555-555555555555');

        $useCase->execute('66666666-6666-6666-6666-666666666666');

        self::assertNotContains(
            'requester.id = :currentUserId OR reviewer.id = :currentUserId OR taskOwner.id = :currentUserId OR projectOwner.id = :currentUserId',
            $whereClauses,
        );
    }

    /**
     * @return array{0: ListTaskRequestsBySprint, 1: array<int, string>}
     */
    private function buildUseCaseWithQueryCapture(bool $isAdminLike, string $currentUserId): array
    {
        $whereClauses = [];

        $query = $this->createMock(AbstractQuery::class);
        $query
            ->method('getResult')
            ->willReturn([]);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->method('addSelect')->willReturnSelf();
        $qb->method('leftJoin')->willReturnSelf();
        $qb->method('orderBy')->willReturnSelf();
        $qb->method('addOrderBy')->willReturnSelf();
        $qb->method('setParameter')->willReturnSelf();
        $qb->method('getQuery')->willReturn($query);
        $qb
            ->method('andWhere')
            ->willReturnCallback(function (string $clause) use (&$whereClauses, $qb): QueryBuilder {
                $whereClauses[] = $clause;

                return $qb;
            });

        $repository = $this->createMock(TaskRequestRepositoryInterface::class);
        $repository
            ->method('createQueryBuilder')
            ->with('tr')
            ->willReturn($qb);

        $user = $this->createMock(User::class);
        $user
            ->method('getId')
            ->willReturn($currentUserId);

        $currentTaskUserProvider = $this->createMock(CurrentTaskUserProvider::class);
        $currentTaskUserProvider
            ->method('getCurrentUser')
            ->willReturn($user);

        $taskAccessService = $this->createMock(TaskAccessServiceInterface::class);
        $taskAccessService
            ->method('isAdminLike')
            ->with($user)
            ->willReturn($isAdminLike);

        return [
            new ListTaskRequestsBySprint(
                $repository,
                $currentTaskUserProvider,
                $taskAccessService,
            ),
            $whereClauses,
        ];
    }
}
