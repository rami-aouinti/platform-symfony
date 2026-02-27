<?php

declare(strict_types=1);

namespace App\Blog\Transport\Controller\Api\V1\BlogTag;

use App\Blog\Application\DTO\BlogTag\BlogTagCreate;
use App\Blog\Application\DTO\BlogTag\BlogTagPatch;
use App\Blog\Application\DTO\BlogTag\BlogTagUpdate;
use App\Blog\Application\Resource\BlogTagResource;
use App\Blog\Application\Resource\Interfaces\BlogTagResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for BlogTagController endpoints.
 *
 * @method BlogTagResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Blog\Transport\Controller\Api\V1\BlogTag
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/v1/blog-tags')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Blog')]
class BlogTagController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => BlogTagCreate::class,
        Controller::METHOD_UPDATE => BlogTagUpdate::class,
        Controller::METHOD_PATCH => BlogTagPatch::class,
    ];

    public function __construct(BlogTagResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
