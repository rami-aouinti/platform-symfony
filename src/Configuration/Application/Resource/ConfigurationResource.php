<?php

declare(strict_types=1);

namespace App\Configuration\Application\Resource;

use App\Configuration\Application\Resource\Interfaces\ConfigurationResourceInterface;
use App\Configuration\Domain\Entity\Configuration as Entity;
use App\Configuration\Domain\Repository\Interfaces\ConfigurationRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;
use App\User\Domain\Entity\UserProfile;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Configuration\Application\Resource
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class ConfigurationResource extends RestResource implements ConfigurationResourceInterface
{
    public function __construct(
        protected readonly RepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }

    public function findByProfileAndKeyName(UserProfile $profile, ?string $keyName = null): array
    {
        /** @var RepositoryInterface $repository */
        $repository = $this->repository;

        return $repository->findByProfileAndKeyName($profile, $keyName);
    }
}
