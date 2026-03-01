<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\Recruit\Application\Resource\Interfaces\SkillResourceInterface;
use App\Recruit\Domain\Repository\Interfaces\SkillRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;

class SkillResource extends RestResource implements SkillResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
