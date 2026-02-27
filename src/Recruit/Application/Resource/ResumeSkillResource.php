<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\Recruit\Application\Resource\Interfaces\ResumeSkillResourceInterface;
use App\Recruit\Domain\Repository\Interfaces\ResumeSkillRepositoryInterface as RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ResumeSkillResource extends AbstractResumeChildResource implements ResumeSkillResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        AuthorizationCheckerInterface $authorizationChecker,
    ) {
        parent::__construct($authorizationChecker, $repository);
    }
}
