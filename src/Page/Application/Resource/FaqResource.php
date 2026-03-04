<?php

declare(strict_types=1);

namespace App\Page\Application\Resource;

use App\General\Application\Rest\RestResource;
use App\Page\Application\Resource\Interfaces\FaqResourceInterface;
use App\Page\Domain\Repository\Interfaces\FaqRepositoryInterface as RepositoryInterface;

class FaqResource extends RestResource implements FaqResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
