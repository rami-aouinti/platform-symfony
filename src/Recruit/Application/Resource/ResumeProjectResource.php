<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\Recruit\Application\Resource\Interfaces\ResumeProjectResourceInterface;
use App\Recruit\Domain\Repository\Interfaces\ResumeProjectRepositoryInterface as RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ResumeProjectResource extends AbstractResumeChildResource implements ResumeProjectResourceInterface
{
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
    ) {
        parent::__construct($authorizationChecker, $repository);
    }
}
