<?php

declare(strict_types=1);

namespace App\Notification\Application\Resource;

use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceFindMethod;
use App\General\Application\Rest\Traits\Methods\ResourceFindOneMethod;
use App\Notification\Application\Resource\Interfaces\NotificationResourceInterface;
use App\Notification\Domain\Entity\Notification as Entity;
use App\Notification\Domain\Repository\Interfaces\NotificationRepositoryInterface as RepositoryInterface;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class NotificationResource extends RestSmallResource implements NotificationResourceInterface
{
    use ResourceFindMethod;
    use ResourceFindOneMethod;

    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
