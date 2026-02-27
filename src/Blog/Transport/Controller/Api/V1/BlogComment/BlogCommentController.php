<?php

declare(strict_types=1);

namespace App\Blog\Transport\Controller\Api\V1\BlogComment;

use App\Blog\Application\DTO\BlogComment\BlogCommentCreate;
use App\Blog\Application\DTO\BlogComment\BlogCommentPatch;
use App\Blog\Application\DTO\BlogComment\BlogCommentUpdate;
use App\Blog\Application\Resource\BlogCommentResource;
use App\Blog\Application\Resource\Interfaces\BlogCommentResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for BlogCommentController endpoints.
 *
 * @method BlogCommentResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Blog\Transport\Controller\Api\V1\BlogComment
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/v1/blog-comments')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Blog')]
class BlogCommentController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => BlogCommentCreate::class,
        Controller::METHOD_UPDATE => BlogCommentUpdate::class,
        Controller::METHOD_PATCH => BlogCommentPatch::class,
    ];

    public function __construct(BlogCommentResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
