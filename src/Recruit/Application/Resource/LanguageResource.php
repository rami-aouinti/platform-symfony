<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\Recruit\Application\Resource\Interfaces\LanguageResourceInterface;
use App\Recruit\Domain\Repository\Interfaces\LanguageRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;

class LanguageResource extends RestResource implements LanguageResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
