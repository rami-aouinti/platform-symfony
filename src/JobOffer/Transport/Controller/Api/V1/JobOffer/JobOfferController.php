<?php

declare(strict_types=1);

namespace App\JobOffer\Transport\Controller\Api\V1\JobOffer;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\JobOffer\Application\DTO\JobOffer\JobOfferCreate;
use App\JobOffer\Application\DTO\JobOffer\JobOfferPatch;
use App\JobOffer\Application\DTO\JobOffer\JobOfferUpdate;
use App\JobOffer\Application\Resource\Interfaces\JobOfferResourceInterface;
use App\JobOffer\Application\Resource\JobOfferResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method JobOfferResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/job-offers')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Job Offer Management')]
class JobOfferController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => JobOfferCreate::class,
        Controller::METHOD_UPDATE => JobOfferUpdate::class,
        Controller::METHOD_PATCH => JobOfferPatch::class,
    ];

    public function __construct(JobOfferResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
