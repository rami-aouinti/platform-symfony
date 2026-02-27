<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\Recruit\Application\Resource\Interfaces\ResumeEducationResourceInterface;
use App\Recruit\Domain\Repository\Interfaces\ResumeEducationRepositoryInterface as RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @package App\Recruit\Application\Resource
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ResumeEducationResource extends AbstractResumeChildResource implements ResumeEducationResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        AuthorizationCheckerInterface $authorizationChecker,
    ) {
        parent::__construct($authorizationChecker, $repository);
    }
}
