<?php

declare(strict_types=1);

namespace App\Recruit\Transport\Controller\Api\V1\Resume;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\General\Transport\Rest\Traits\Methods\DeleteMethod;
use App\General\Transport\Rest\Traits\Methods\FindOneMethod;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use App\Recruit\Application\DTO\ResumeReference\ResumeReferenceCreate;
use App\Recruit\Application\DTO\ResumeReference\ResumeReferencePatch;
use App\Recruit\Application\DTO\ResumeReference\ResumeReferenceUpdate;
use App\Recruit\Application\Resource\Interfaces\ResumeReferenceResourceInterface;
use App\Recruit\Application\Resource\Interfaces\ResumeResourceInterface;
use App\Recruit\Domain\Entity\Resume;
use App\User\Application\Security\Permission;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[AsController]
#[Route(path: '/api/v1/me/resumes/{resumeId}/references', requirements: ['resumeId' => Requirement::UUID_V1])]
#[IsGranted(new Expression("is_granted('IS_AUTHENTICATED_FULLY')"))]
#[OA\Tag(name: 'ResumeReference Management')]
class ResumeReferenceController extends Controller
{
    use FindOneMethod;
    use CreateMethod;
    use UpdateMethod;
    use PatchMethod;
    use DeleteMethod;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ResumeReferenceCreate::class,
        Controller::METHOD_UPDATE => ResumeReferenceUpdate::class,
        Controller::METHOD_PATCH => ResumeReferencePatch::class,
    ];

    public function __construct(
        ResumeReferenceResourceInterface $resource,
        private readonly ResumeResourceInterface $resumeResource,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
        parent::__construct($resource);
    }

    #[Route(path: '', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'List resume references')]
    #[OA\Response(response: 200, description: 'List', content: new JsonContent(type: 'array', items: new OA\Items(type: 'object')))]
    #[OA\Response(response: 403, description: 'Forbidden')]
    #[OA\Response(response: 404, description: 'Resume not found')]
    public function findAction(Request $request, string $resumeId): Response
    {
        $resume = $this->getOwnedResume($resumeId, Permission::RESUME_VIEW);
        $criteria = RequestHandler::getCriteria($request);
        $criteria['resume'] = $resume;

        $data = $this->getResource()->find(
            $criteria,
            RequestHandler::getOrderBy($request),
            RequestHandler::getLimit($request),
            RequestHandler::getOffset($request),
            RequestHandler::getSearchTerms($request),
            RequestHandler::getTenant($request),
        );

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }

    #[Route(path: '', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Create resume references item')]
    #[OA\RequestBody(required: true, content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 201, description: 'Created', content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 403, description: 'Forbidden')]
    #[OA\Response(response: 404, description: 'Resume not found')]
    public function createAction(Request $request, ResumeReferenceCreate $restDto, string $resumeId): Response
    {
        $restDto->setResume($this->getOwnedResume($resumeId, Permission::RESUME_EDIT));

        return $this->createMethod($request, $restDto);
    }

    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'Get one resume references item')]
    #[OA\Response(response: 200, description: 'Item', content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 403, description: 'Forbidden')]
    #[OA\Response(response: 404, description: 'Resource not found')]
    public function findOneAction(Request $request, string $resumeId, string $id): Response
    {
        $this->assertChildBelongsToResume($resumeId, $id, Permission::RESUME_VIEW);

        return $this->findOneMethod($request, $id);
    }

    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PUT])]
    #[OA\Put(summary: 'Update resume references item')]
    #[OA\RequestBody(required: true, content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Updated', content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 403, description: 'Forbidden')]
    #[OA\Response(response: 404, description: 'Resource not found')]
    public function updateAction(Request $request, ResumeReferenceUpdate $restDto, string $resumeId, string $id): Response
    {
        $restDto->setResume($this->assertChildBelongsToResume($resumeId, $id, Permission::RESUME_EDIT));

        return $this->updateMethod($request, $restDto, $id);
    }

    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    #[OA\Patch(summary: 'Patch resume references item')]
    #[OA\RequestBody(required: true, content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Patched', content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 403, description: 'Forbidden')]
    #[OA\Response(response: 404, description: 'Resource not found')]
    public function patchAction(Request $request, ResumeReferencePatch $restDto, string $resumeId, string $id): Response
    {
        $restDto->setResume($this->assertChildBelongsToResume($resumeId, $id, Permission::RESUME_EDIT));

        return $this->patchMethod($request, $restDto, $id);
    }

    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_DELETE])]
    #[OA\Delete(summary: 'Delete resume references item')]
    #[OA\Response(response: 200, description: 'Deleted', content: new JsonContent(type: 'object'))]
    #[OA\Response(response: 403, description: 'Forbidden')]
    #[OA\Response(response: 404, description: 'Resource not found')]
    public function deleteAction(Request $request, string $resumeId, string $id): Response
    {
        $this->assertChildBelongsToResume($resumeId, $id, Permission::RESUME_EDIT);

        return $this->deleteMethod($request, $id);
    }

    private function assertChildBelongsToResume(string $resumeId, string $id, Permission $permission): Resume
    {
        $resume = $this->getOwnedResume($resumeId, $permission);
        $child = $this->getResource()->findOne($id);

        if (!method_exists($child, 'getResume') || $child->getResume()?->getId() !== $resume->getId()) {
            throw new NotFoundHttpException('Resource not found.');
        }

        return $resume;
    }

    private function getOwnedResume(string $resumeId, Permission $permission): Resume
    {
        try {
            $resume = $this->resumeResource->findOne($resumeId);
        } catch (Throwable) {
            throw new NotFoundHttpException('Resume not found.');
        }

        if (!$this->authorizationChecker->isGranted($permission->value, $resume)) {
            throw new AccessDeniedHttpException('Forbidden.');
        }

        return $resume;
    }
}
