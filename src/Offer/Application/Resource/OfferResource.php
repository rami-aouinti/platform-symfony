<?php

declare(strict_types=1);

namespace App\Offer\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Offer\Application\Resource\Interfaces\OfferResourceInterface;
use App\Offer\Domain\Entity\Offer as Entity;
use App\Offer\Domain\Repository\Interfaces\OfferRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class OfferResource extends RestResource implements OfferResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
    ) {
        parent::__construct($repository);
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity) {
            return;
        }

        $entity->setCreatedBy($this->userTypeIdentification->getUser());
    }
}
