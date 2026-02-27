<?php

declare(strict_types=1);

namespace App\Blog\Application\Resource;

use App\Blog\Application\Resource\Interfaces\BlogTagResourceInterface;
use App\Blog\Domain\Repository\Interfaces\BlogTagRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestResource;

/**
 * BlogTagResource.
 *
 * @package App\Blog\Application\Resource
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class BlogTagResource extends RestResource implements BlogTagResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
