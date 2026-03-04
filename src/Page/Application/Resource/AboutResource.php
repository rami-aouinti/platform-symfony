<?php

declare(strict_types=1);

namespace App\Page\Application\Resource;

use App\General\Application\Rest\RestResource;
use App\Page\Application\Resource\Interfaces\AboutResourceInterface;
use App\Page\Domain\Repository\Interfaces\AboutRepositoryInterface as RepositoryInterface;

class AboutResource extends RestResource implements AboutResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
