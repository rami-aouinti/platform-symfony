<?php

declare(strict_types=1);

namespace App\Page\Application\Resource;

use App\General\Application\Rest\RestResource;
use App\Page\Application\Resource\Interfaces\ContactResourceInterface;
use App\Page\Domain\Repository\Interfaces\ContactRepositoryInterface as RepositoryInterface;

class ContactResource extends RestResource implements ContactResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
