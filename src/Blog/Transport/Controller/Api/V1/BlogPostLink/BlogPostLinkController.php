<?php

declare(strict_types=1);

namespace App\Blog\Transport\Controller\Api\V1\BlogPostLink;

use App\Blog\Application\DTO\BlogPostLink\BlogPostLinkCreate;
use App\Blog\Application\DTO\BlogPostLink\BlogPostLinkPatch;
use App\Blog\Application\DTO\BlogPostLink\BlogPostLinkUpdate;
use App\Blog\Application\Resource\BlogPostLinkResource;
use App\Blog\Application\Resource\Interfaces\BlogPostLinkResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for BlogPostLinkController endpoints.
 *
 * @method BlogPostLinkResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Blog\Transport\Controller\Api\V1\BlogPostLink
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/api/v1/admin/blog-post-links')]
#[Route(path: '/v1/blog-post-links')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Blog')]
class BlogPostLinkController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;
    use Actions\Authenticated\SchemaAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => BlogPostLinkCreate::class,
        Controller::METHOD_UPDATE => BlogPostLinkUpdate::class,
        Controller::METHOD_PATCH => BlogPostLinkPatch::class,
    ];

    public function __construct(BlogPostLinkResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
