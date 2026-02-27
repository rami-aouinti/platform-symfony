<?php

declare(strict_types=1);

namespace App\User\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\User\Domain\Entity\User as Entity;
use App\User\Domain\Entity\UserGroup;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface as Repository;
use Throwable;

/**
 * @package App\User\Application\Resource
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 * @codingStandardsIgnoreStart
 *
 * @method Entity getReference(string $id, ?string $entityManagerName = null)
 * @method \App\User\Infrastructure\Repository\UserRepository getRepository()
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @method Entity|null findOne(string $id, ?bool $throwExceptionIfNotFound = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?bool $throwExceptionIfNotFound = null, ?string $entityManagerName = null)
 * @method Entity create(RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 * @method Entity update(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 * @method Entity patch(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 * @method Entity delete(string $id, ?bool $flush = null, ?string $entityManagerName = null)
 * @method Entity save(EntityInterface $entity, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 *
 * @codingStandardsIgnoreEnd
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class UserResource extends RestResource
{
    /**
     * @param \App\User\Infrastructure\Repository\UserRepository $repository
     */
    public function __construct(Repository $repository)
    {
        parent::__construct($repository);
    }

    public function isLegacyProfileFieldsEnabled(): bool
    {
        return true;
    }

    public function getProfileContractVersion(): string
    {
        return 'v1+v2';
    }

    /**
     * Method to fetch users for specified user group, note that this method will also check user role inheritance so
     * return value will contain all users that belong to specified user group via role inheritance.
     *
     * @throws Throwable
     *
     * @return array<int, Entity>
     */
    public function getUsersForGroup(UserGroup $userGroup): array
    {
        return $this->getRepository()->findByGroupOrInheritedRole($userGroup);
    }
}
