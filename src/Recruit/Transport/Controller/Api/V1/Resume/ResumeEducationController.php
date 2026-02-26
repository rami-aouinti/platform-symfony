<?php

declare(strict_types=1);

namespace App\Recruit\Transport\Controller\Api\V1\Resume;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Recruit\Application\DTO\ResumeEducation\ResumeEducationCreate;
use App\Recruit\Application\DTO\ResumeEducation\ResumeEducationPatch;
use App\Recruit\Application\DTO\ResumeEducation\ResumeEducationUpdate;
use App\Recruit\Application\Resource\Interfaces\ResumeEducationResourceInterface;
use App\Recruit\Application\Resource\ResumeEducationResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method ResumeEducationResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/v1/resume-education')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Resume Education Management')]
class ResumeEducationController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ResumeEducationCreate::class,
        Controller::METHOD_UPDATE => ResumeEducationUpdate::class,
        Controller::METHOD_PATCH => ResumeEducationPatch::class,
    ];

    public function __construct(ResumeEducationResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
