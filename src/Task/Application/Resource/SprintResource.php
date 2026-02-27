<?php

declare(strict_types=1);

namespace App\Task\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Application\Resource\Interfaces\SprintResourceInterface;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Sprint as Entity;
use App\Task\Domain\Repository\Interfaces\SprintRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class SprintResource extends RestResource implements SprintResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly TaskAccessServiceInterface $taskAccessService,
    ) {
        parent::__construct($repository);
    }


    public function findByCompany(string $companyId, ?bool $active = null): array
    {
        /** @var RepositoryInterface $repository */
        $repository = $this->getRepository();

        return $repository->findByCompany($companyId, $active);
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertAdminLike();
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertAdminLike();
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertAdminLike();
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        $this->assertAdminLike();
    }

    private function assertAdminLike(): void
    {
        if ($this->taskAccessService->isAdminLike($this->getCurrentUser())) {
            return;
        }

        throw new AccessDeniedHttpException('Only admin can manage sprints.');
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }
}
