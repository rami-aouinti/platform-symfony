<?php

declare(strict_types=1);

namespace App\Quiz\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\AbstractOwnedResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Quiz\Application\Resource\Interfaces\QuizResourceInterface;
use App\Quiz\Application\Service\Interfaces\QuizAccessServiceInterface;
use App\Quiz\Domain\Entity\Quiz as Entity;
use App\Quiz\Domain\Repository\Interfaces\QuizRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class QuizResource extends AbstractOwnedResource implements QuizResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        UserTypeIdentification $userTypeIdentification,
        private readonly QuizAccessServiceInterface $quizAccessService,
    ) {
        parent::__construct($repository, $userTypeIdentification);
    }

    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
        $currentUser = $this->getCurrentUserOrDeny();

        if ($this->quizAccessService->isAdminLike($currentUser)) {
            return;
        }

        $criteria['owner'] = $currentUser;
    }

    protected function onBeforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $entity->setOwner($this->getCurrentUserOrDeny());
        }
    }

    protected function authorizeBeforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageQuiz($entity);
        }
    }

    protected function authorizeBeforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageQuiz($entity);
        }
    }

    protected function authorizeBeforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageQuiz($entity);
        }
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageQuiz($entity);
        }
    }

    private function assertCanManageQuiz(Entity $quiz): void
    {
        $currentUser = $this->getCurrentUserOrDeny();

        $this->assertOwnerOrDeny(
            $this->quizAccessService->canManageQuiz($currentUser, $quiz),
            'Only quiz owner can manage this quiz.',
        );
    }
}
