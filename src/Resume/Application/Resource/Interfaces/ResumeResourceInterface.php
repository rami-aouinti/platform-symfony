<?php

declare(strict_types=1);

namespace App\Resume\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Resume\Domain\Entity\Resume;

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
