<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceFindMethod;
use App\General\Application\Rest\Traits\Methods\ResourceFindOneMethod;
use App\Recruit\Application\Resource\Interfaces\CandidateProfileResourceInterface;
use App\Recruit\Domain\Entity\CandidateProfile as Entity;
use App\Recruit\Domain\Repository\Interfaces\CandidateProfileRepositoryInterface as RepositoryInterface;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Candidate
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
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
