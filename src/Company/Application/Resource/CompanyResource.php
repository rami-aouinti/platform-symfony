<?php

declare(strict_types=1);

namespace App\Company\Application\Resource;

use App\Company\Application\Resource\Interfaces\CompanyResourceInterface;
use App\Company\Domain\Entity\Company as Entity;
use App\Company\Domain\Repository\Interfaces\CompanyRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceFindMethod;
use App\General\Application\Rest\Traits\Methods\ResourceFindOneMethod;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class CompanyResource extends RestSmallResource implements CompanyResourceInterface
{
    use ResourceFindMethod;
    use ResourceFindOneMethod;

    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
