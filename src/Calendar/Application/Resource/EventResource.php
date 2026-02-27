<?php

declare(strict_types=1);

namespace App\Calendar\Application\Resource;

use App\Calendar\Application\Resource\Interfaces\EventResourceInterface;
use App\Calendar\Domain\Entity\Event as Entity;
use App\Calendar\Domain\Repository\Interfaces\EventRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Calendar\Application\Resource
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class EventResource extends RestResource implements EventResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
