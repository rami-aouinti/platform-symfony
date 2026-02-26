<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Recruit\Domain\Entity\Resume;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface ResumeResourceInterface extends RestResourceInterface
{
    /**
     * @return array<int, Resume>
     */
    public function findMyResumes(
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array;
}
