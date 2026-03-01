<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\ResumeEducation;
use App\Recruit\Domain\Entity\ResumeExperience;
use App\Recruit\Domain\Entity\ResumeLanguage;
use App\Recruit\Domain\Entity\ResumeProject;
use App\Recruit\Domain\Entity\ResumeReference;
use App\Recruit\Domain\Entity\ResumeSkill;
use App\User\Application\Security\Permission;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * AbstractResumeChildResource.
 *
 * @package App\Recruit\Application\Resource
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
abstract class AbstractResumeChildResource extends RestResource
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        BaseRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertCanEdit($entity);
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertCanEdit($entity);
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        $this->assertCanEdit($entity);
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        $this->assertCanEdit($entity);
    }

    protected function assertCanEdit(EntityInterface $entity): void
    {
        if (
            $entity instanceof ResumeExperience
            || $entity instanceof ResumeEducation
            || $entity instanceof ResumeSkill
            || $entity instanceof ResumeLanguage
            || $entity instanceof ResumeReference
            || $entity instanceof ResumeProject
        ) {
            if (!$this->authorizationChecker->isGranted(Permission::RESUME_EDIT->value, $entity->getResume())) {
                throw new AccessDeniedHttpException('Only resume owner can edit this resume.');
            }
        }
    }
}
