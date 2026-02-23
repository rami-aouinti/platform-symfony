<?php

declare(strict_types=1);

namespace App\Candidate\Transport\Controller\Api\V1\Candidate;

use App\Candidate\Application\Resource\CandidateProfileResource;
use App\Candidate\Application\Resource\Interfaces\CandidateProfileResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method CandidateProfileResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/candidates')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Candidate Management')]
class CandidateController extends Controller
{
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;

    public function __construct(CandidateProfileResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
