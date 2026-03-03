<?php

declare(strict_types=1);

namespace App\Task\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Task\Domain\Entity\Project;
use App\User\Domain\Entity\User;

/**
 * ProjectResourceInterface.
 *
 * @package App\Task\Application\Resource\Interfaces
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
interface ProjectResourceInterface extends RestResourceInterface
{
    /**
     * @return array<int, Project>
     */
    public function findMyAccessibleProjects(User $currentUser): array;

    /**
     * @return array<int, Project>
     */
    public function findProjectsForMyCompanyAccess(string $companyId, User $currentUser): array;
}
