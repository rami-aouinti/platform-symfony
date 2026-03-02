<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Repository;

use App\Chat\Domain\Entity\ChatMessage;
use App\Chat\Domain\Entity\ChatMessageReaction as Entity;
use App\Chat\Domain\Repository\Interfaces\ChatMessageReactionRepositoryInterface;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use App\User\Domain\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class ChatMessageReactionRepository extends BaseRepository implements ChatMessageReactionRepositoryInterface
{
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findOneByMessageUserReaction(ChatMessage $message, User $user, string $reaction): ?Entity
    {
        /** @var Entity|null $entity */
        $entity = $this->findOneBy([
            'message' => $message,
            'user' => $user,
            'reaction' => $reaction,
        ]);

        return $entity;
    }

    public function save(EntityInterface $reaction, ?bool $flush = null, ?string $entityManagerName = null): self
    {
        return parent::save($reaction, $flush, $entityManagerName);
    }

    public function remove(EntityInterface $reaction, ?bool $flush = null, ?string $entityManagerName = null): self
    {
        return parent::remove($reaction, $flush, $entityManagerName);
    }
}
