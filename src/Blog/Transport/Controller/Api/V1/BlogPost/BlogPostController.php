<?php

declare(strict_types=1);

namespace App\Blog\Transport\Controller\Api\V1\BlogPost;

use App\Blog\Application\DTO\BlogPost\BlogPost;
use App\Blog\Application\Resource\BlogPostResource;
use App\Blog\Application\Resource\Interfaces\BlogPostResourceInterface;
use App\General\Transport\Rest\CrudController;
use App\General\Transport\Rest\ResponseHandler;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method BlogPostResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/blog-posts')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Blog')]
class BlogPostController extends CrudController
{
    protected static string $dtoBaseClass = BlogPost::class;

    public function __construct(BlogPostResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
