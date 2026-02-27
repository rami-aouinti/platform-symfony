<?php

declare(strict_types=1);

namespace App\Task\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\AbstractOwnedResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Application\Resource\Interfaces\SprintResourceInterface;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Sprint as Entity;
use App\Task\Domain\Repository\Interfaces\SprintRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class SprintResource extends AbstractOwnedResource implements SprintResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        UserTypeIdentification $userTypeIdentification,
        private readonly TaskAccessServiceInterface $taskAccessService,
    ) {
        parent::__construct($repository, $userTypeIdentification);
    }

    public function findByCompany(string $companyId, ?bool $active = null): array
    {
        /** @var RepositoryInterface $repository */
        $repository = $this->getRepository();

        return $repository->findByCompany($companyId, $active);
    }

    protected function authorizeBeforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertAdminLike();
    }

    protected function authorizeBeforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertAdminLike();
    }

    protected function authorizeBeforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertAdminLike();
    }

    protected function authorizeBeforeDelete(string &$id, EntityInterface $entity): void
    {
        $this->assertAdminLike();
    }

    private function assertAdminLike(): void
    {
        $this->assertOwnerOrDeny(
            $this->taskAccessService->isAdminLike($this->getCurrentUserOrDeny()),
            'Only admin can manage sprints.',
        );
    }
}
