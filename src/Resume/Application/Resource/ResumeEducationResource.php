<?php

declare(strict_types=1);

namespace App\Resume\Application\Resource;

use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Resume\Application\Resource\Interfaces\ResumeEducationResourceInterface;
use App\Resume\Domain\Entity\ResumeEducation as Entity;
use App\Resume\Domain\Repository\Interfaces\ResumeEducationRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\Permission;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ResumeEducationResource extends RestResource implements ResumeEducationResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
        parent::__construct($repository);
    }

    public function beforeCreate($restDto, EntityInterface $entity): void
    {
        $this->assertCanEdit($entity);
    }
    public function beforeUpdate(string &$id, $restDto, EntityInterface $entity): void
    {
        $this->assertCanEdit($entity);
    }
    public function beforePatch(string &$id, $restDto, EntityInterface $entity): void
    {
        $this->assertCanEdit($entity);
    }
    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        $this->assertCanEdit($entity);
    }

    private function assertCanEdit(EntityInterface $entity): void
    {
        if ($entity instanceof Entity && !$this->authorizationChecker->isGranted(Permission::RESUME_EDIT->value, $entity->getResume())) {
            throw new AccessDeniedHttpException('Only resume owner can edit this resume.');
        }
    }
}
