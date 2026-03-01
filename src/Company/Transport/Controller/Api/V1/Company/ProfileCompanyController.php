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
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for authenticated user company endpoints.
 *
 * @method CompanyResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/api/v1/me/profile/companies')]
#[IsGranted('ROLE_LOGGED')]
#[OA\Tag(name: 'Me - Company')]
class ProfileCompanyController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;
    use Actions\Authenticated\SchemaAction;

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
