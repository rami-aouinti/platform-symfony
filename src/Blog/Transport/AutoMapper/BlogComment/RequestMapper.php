<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogComment;

use App\Blog\Application\Resource\BlogPostResource;
use App\Blog\Domain\Entity\BlogPost;
use App\General\Transport\AutoMapper\RestRequestMapper;
use Throwable;

/**
 * RequestMapper.
 *
 * @package App\Blog\Transport\AutoMapper\BlogComment
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'post',
        'content',
        'referenceType',
        'referenceId',
    ];

    public function __construct(
        private readonly BlogPostResource $blogPostResource
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
}
