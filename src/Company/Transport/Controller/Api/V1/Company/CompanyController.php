<?php

declare(strict_types=1);

namespace App\Company\Transport\Controller\Api\V1\Company;

use App\Company\Application\DTO\Company\CompanyCreate;
use App\Company\Application\DTO\Company\CompanyPatch;
use App\Company\Application\DTO\Company\CompanyUpdate;
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
 * API controller for CompanyController endpoints.
 *
 * @method CompanyResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Company\Transport\Controller\Api\V1\Company
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/v1/companies')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Company Management')]
class CompanyController extends Controller
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
        Controller::METHOD_CREATE => CompanyCreate::class,
        Controller::METHOD_UPDATE => CompanyUpdate::class,
        Controller::METHOD_PATCH => CompanyPatch::class,
    ];

    public function __construct(CompanyResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
