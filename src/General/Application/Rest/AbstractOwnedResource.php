<?php

declare(strict_types=1);

namespace App\General\Application\Rest;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * AbstractOwnedResource.
 *
 * @package App\General\Application\Rest
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
abstract class AbstractOwnedResource extends RestResource
{
    public function __construct(
        BaseRepositoryInterface $repository,
        protected readonly UserTypeIdentification $userTypeIdentification,
    ) {
        parent::__construct($repository);
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->authorizeBeforeCreate($restDto, $entity);
        $this->onBeforeCreate($restDto, $entity);
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->authorizeBeforeUpdate($id, $restDto, $entity);
        $this->onBeforeUpdate($id, $restDto, $entity);
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->authorizeBeforePatch($id, $restDto, $entity);
        $this->onBeforePatch($id, $restDto, $entity);
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        $this->authorizeBeforeDelete($id, $entity);
        $this->onBeforeDelete($id, $entity);
    }

    protected function authorizeBeforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
    }

    protected function authorizeBeforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
    }

    protected function authorizeBeforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
    }

    protected function authorizeBeforeDelete(string &$id, EntityInterface $entity): void
    {
    }

    protected function onBeforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
    }

    protected function onBeforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
    }

    protected function onBeforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
    }

    protected function onBeforeDelete(string &$id, EntityInterface $entity): void
    {
    }

    protected function getCurrentUserOrDeny(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }

    protected function assertOwnerOrDeny(bool $isAllowed, string $message): void
    {
        if ($isAllowed) {
            return;
        }

        throw new AccessDeniedHttpException($message);
    }
}
