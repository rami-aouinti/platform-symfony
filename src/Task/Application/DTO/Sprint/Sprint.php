<?php

declare(strict_types=1);

namespace App\Task\Application\DTO\Sprint;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Domain\Entity\Sprint as Entity;
use App\Task\Domain\Entity\TaskRequest;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class Sprint extends RestDto
{
    protected static array $mappings = [
        'taskRequests' => 'mapTaskRequests',
    ];

    #[Assert\NotBlank]
    #[Assert\DateTime]
    protected ?string $startDate = null;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    protected ?string $endDate = null;

    /**
     * @var array<int, TaskRequest>
     */
    #[AppAssert\EntityReferenceExists(TaskRequest::class)]
    protected array $taskRequests = [];

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function setStartDate(?string $startDate): self
    {
        $this->setVisited('startDate');
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function setEndDate(?string $endDate): self
    {
        $this->setVisited('endDate');
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return array<int, TaskRequest>
     */
    public function getTaskRequests(): array
    {
        return $this->taskRequests;
    }

    /**
     * @param array<int, TaskRequest> $taskRequests
     */
    public function setTaskRequests(array $taskRequests): self
    {
        $this->setVisited('taskRequests');
        $this->taskRequests = $taskRequests;

        return $this;
    }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->startDate = $entity->getStartDate()?->format(DateTimeImmutable::ATOM);
            $this->endDate = $entity->getEndDate()?->format(DateTimeImmutable::ATOM);
            $this->taskRequests = $entity->getTaskRequests()->toArray();
        }

        return $this;
    }

    /**
     * @param array<int, TaskRequest> $taskRequests
     */
    protected function mapTaskRequests(Entity $entity, array $taskRequests): void
    {
        /** @var Collection<int, TaskRequest> $existingTaskRequests */
        $existingTaskRequests = $entity->getTaskRequests();

        foreach ($existingTaskRequests as $existingTaskRequest) {
            $entity->removeTaskRequest($existingTaskRequest);
        }

        foreach ($taskRequests as $taskRequest) {
            $entity->addTaskRequest($taskRequest);
        }
    }
}
