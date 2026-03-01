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
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for BlogPostController endpoints.
 *
 * @method BlogPostResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Blog\Transport\Controller\Api\V1\BlogPost
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/api/v1/admin/blog-posts')]
#[Route(path: '/v1/blog-posts')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Blog')]
class BlogPostController extends CrudController
{
    private const string READ_CACHE_SCOPE = 'blog';

    protected static string $dtoBaseClass = BlogPost::class;

    public function __construct(BlogPostResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    protected function getReadCacheScope(): ?string
    {
        return self::READ_CACHE_SCOPE;
    }
}
