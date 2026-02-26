<?php

declare(strict_types=1);

namespace App\Recruit\Transport\Controller\Api\V1\Resume;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Recruit\Application\DTO\ResumeSkill\ResumeSkillCreate;
use App\Recruit\Application\DTO\ResumeSkill\ResumeSkillPatch;
use App\Recruit\Application\DTO\ResumeSkill\ResumeSkillUpdate;
use App\Recruit\Application\Resource\Interfaces\ResumeSkillResourceInterface;
use App\Recruit\Application\Resource\ResumeSkillResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method ResumeSkillResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/v1/resume-skills')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Resume Skill Management')]
class ResumeSkillController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ResumeSkillCreate::class,
        Controller::METHOD_UPDATE => ResumeSkillUpdate::class,
        Controller::METHOD_PATCH => ResumeSkillPatch::class,
    ];

    public function __construct(ResumeSkillResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
