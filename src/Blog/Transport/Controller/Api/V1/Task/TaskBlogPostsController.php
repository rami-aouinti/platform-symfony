<?php

declare(strict_types=1);

namespace App\Blog\Transport\Controller\Api\V1\Task;

use App\Blog\Application\Resource\BlogPostLinkResource;
use App\Blog\Application\Resource\Interfaces\BlogPostLinkResourceInterface;
use App\Blog\Domain\Enum\BlogReferenceType;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for TaskBlogPostsController endpoints.
 *
 * @method BlogPostLinkResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Blog\Transport\Controller\Api\V1\Task
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/api/v1/admin/tasks')]
#[Route(path: '/v1/tasks')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Blog')]
class TaskBlogPostsController extends Controller
{
    public function __construct(BlogPostLinkResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{id}/blog-posts', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function taskBlogPostsAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->find(criteria: [
                'referenceType' => BlogReferenceType::TASK->value,
                'task' => $id,
            ]),
            $this->getResource(),
        );
    }
}
