<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Resource;

use App\ApplicationCatalog\Application\Resource\Interfaces\AdminApplicationResourceInterface;
use App\ApplicationCatalog\Domain\Entity\Application as Entity;
use App\ApplicationCatalog\Domain\Repository\Interfaces\ApplicationRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
final class AdminApplicationResource extends RestResource implements AdminApplicationResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
