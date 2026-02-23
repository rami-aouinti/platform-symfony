<?php

declare(strict_types=1);

namespace App\Candidate\Application\Resource;

use App\Candidate\Application\Resource\Interfaces\CandidateProfileResourceInterface;
use App\Candidate\Domain\Entity\CandidateProfile as Entity;
use App\Candidate\Domain\Repository\Interfaces\CandidateProfileRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceFindMethod;
use App\General\Application\Rest\Traits\Methods\ResourceFindOneMethod;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class CandidateProfileResource extends RestSmallResource implements CandidateProfileResourceInterface
{
    use ResourceFindMethod;
    use ResourceFindOneMethod;

    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
