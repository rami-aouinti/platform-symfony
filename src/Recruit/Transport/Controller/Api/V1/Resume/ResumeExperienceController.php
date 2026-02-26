<?php

declare(strict_types=1);

namespace App\Recruit\Transport\Controller\Api\V1\Resume;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Recruit\Application\DTO\ResumeExperience\ResumeExperienceCreate;
use App\Recruit\Application\DTO\ResumeExperience\ResumeExperiencePatch;
use App\Recruit\Application\DTO\ResumeExperience\ResumeExperienceUpdate;
use App\Recruit\Application\Resource\Interfaces\ResumeExperienceResourceInterface;
use App\Recruit\Application\Resource\ResumeExperienceResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method ResumeExperienceResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/v1/resume-experiences')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Resume Experience Management')]
class ResumeExperienceController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ResumeExperienceCreate::class,
        Controller::METHOD_UPDATE => ResumeExperienceUpdate::class,
        Controller::METHOD_PATCH => ResumeExperiencePatch::class,
    ];

    public function __construct(ResumeExperienceResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
