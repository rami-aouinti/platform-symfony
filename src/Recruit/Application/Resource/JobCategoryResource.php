<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\Recruit\Application\Resource\Interfaces\JobCategoryResourceInterface;
use App\Recruit\Domain\Repository\Interfaces\JobCategoryRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;

class JobCategoryResource extends RestResource implements JobCategoryResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
