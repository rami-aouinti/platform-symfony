<?php

declare(strict_types=1);

namespace App\Company\Application\Resource;

use App\Company\Application\Resource\Interfaces\CompanyMembershipResourceInterface;
use App\Company\Domain\Entity\CompanyMembership as Entity;
use App\Company\Domain\Repository\Interfaces\CompanyMembershipRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceFindMethod;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Company\Application\Resource
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class CompanyMembershipResource extends RestSmallResource implements CompanyMembershipResourceInterface
{
    use ResourceFindMethod;

    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
