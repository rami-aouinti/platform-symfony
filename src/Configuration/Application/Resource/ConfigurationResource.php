<?php

declare(strict_types=1);

namespace App\Configuration\Application\Resource;

use App\Configuration\Application\Resource\Interfaces\ConfigurationResourceInterface;
use App\Configuration\Domain\Entity\Configuration as Entity;
use App\Configuration\Domain\Repository\Interfaces\ConfigurationRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class ConfigurationResource extends RestResource implements ConfigurationResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
