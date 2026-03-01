<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\Recruit\Application\Resource\Interfaces\ResumeLanguageResourceInterface;
use App\Recruit\Domain\Repository\Interfaces\ResumeLanguageRepositoryInterface as RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ResumeLanguageResource extends AbstractResumeChildResource implements ResumeLanguageResourceInterface
{
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
    ) {
        parent::__construct($authorizationChecker, $repository);
    }
}
