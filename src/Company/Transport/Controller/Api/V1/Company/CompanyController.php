<?php

declare(strict_types=1);

namespace App\Company\Transport\Controller\Api\V1\Company;

use App\Company\Application\Resource\CompanyResource;
use App\Company\Application\Resource\Interfaces\CompanyResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method CompanyResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/companies')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Company Management')]
class CompanyController extends Controller
{
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;

    public function __construct(CompanyResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
