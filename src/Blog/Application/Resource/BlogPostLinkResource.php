<?php

declare(strict_types=1);

namespace App\Blog\Application\Resource;

use App\Blog\Application\Resource\Interfaces\BlogPostLinkResourceInterface;
use App\Blog\Domain\Repository\Interfaces\BlogPostLinkRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;

class BlogPostLinkResource extends RestResource implements BlogPostLinkResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
