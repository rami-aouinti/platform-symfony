<?php

declare(strict_types=1);

namespace App\Blog\Transport\Controller\Api\V1\BlogPost;

use App\Blog\Application\DTO\BlogPost\BlogPostCreate;
use App\Blog\Application\DTO\BlogPost\BlogPostPatch;
use App\Blog\Application\DTO\BlogPost\BlogPostUpdate;
use App\Blog\Application\Resource\BlogPostResource;
use App\Blog\Application\Resource\Interfaces\BlogPostResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
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
class BlogPostController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => BlogPostCreate::class,
        Controller::METHOD_UPDATE => BlogPostUpdate::class,
        Controller::METHOD_PATCH => BlogPostPatch::class,
    ];

    public function __construct(BlogPostResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
