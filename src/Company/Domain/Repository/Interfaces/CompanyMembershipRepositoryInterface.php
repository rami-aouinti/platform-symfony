<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository\Interfaces;

use App\Company\Domain\Entity\CompanyMembership;
use App\General\Domain\Entity\Interfaces\EntityInterface;

/**
 * @package App\Company\Domain\Repository\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface CompanyMembershipRepositoryInterface
{
    /**
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?object;

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<int, object|EntityInterface>
     */
    public function findBy(array $criteria): array;

    public function save(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): self;

    public function remove(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): self;
}
