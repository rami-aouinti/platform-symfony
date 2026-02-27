<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogPostLink;

use App\Blog\Application\Resource\BlogPostResource;
use App\Blog\Domain\Entity\BlogPost;
use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Task\Application\Resource\TaskRequestResource;
use App\Task\Application\Resource\TaskResource;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use Throwable;

/**
 * RequestMapper.
 *
 * @package App\Blog\Transport\AutoMapper\BlogPostLink
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'post',
        'task',
        'taskRequest',
        'referenceType',
    ];

    public function __construct(
        private readonly BlogPostResource $blogPostResource,
        private readonly TaskResource $taskResource,
        private readonly TaskRequestResource $taskRequestResource,
    ) {
    }

    protected function transformPost(?string $post): ?BlogPost
    {
        if ($post === null || $post === '') {
            return null;
        }

        try {
            return $this->blogPostResource->getReference($post);
        } catch (Throwable) {
            return null;
        }
    }

    protected function transformTask(?string $task): ?Task
    {
        if ($task === null || $task === '') {
            return null;
        }

        try {
            return $this->taskResource->getReference($task);
        } catch (Throwable) {
            return null;
        }
    }

    protected function transformTaskRequest(?string $taskRequest): ?TaskRequest
    {
        if ($taskRequest === null || $taskRequest === '') {
            return null;
        }

        try {
            return $this->taskRequestResource->getReference($taskRequest);
        } catch (Throwable) {
            return null;
        }
    }
}
