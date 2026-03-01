<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\Recruit\Application\Resource\Interfaces\ResumeReferenceResourceInterface;
use App\Recruit\Domain\Repository\Interfaces\ResumeReferenceRepositoryInterface as RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ResumeReferenceResource extends AbstractResumeChildResource implements ResumeReferenceResourceInterface
{
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
    ) {
        parent::__construct($authorizationChecker, $repository);
    }
}
