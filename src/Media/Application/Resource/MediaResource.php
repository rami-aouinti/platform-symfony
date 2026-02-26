<?php

declare(strict_types=1);

namespace App\Media\Application\Resource;

use App\General\Application\Rest\RestResource;
use App\Media\Application\Resource\Interfaces\MediaResourceInterface;
use App\Media\Domain\Entity\Media as Entity;
use App\Media\Domain\Repository\Interfaces\MediaRepositoryInterface as RepositoryInterface;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class MediaResource extends RestResource implements MediaResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
