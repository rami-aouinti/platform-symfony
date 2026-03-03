<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Transport\Controller\Api\V1\AdminApplication;

use App\ApplicationCatalog\Application\DTO\AdminApplication\AdminApplication;
use App\ApplicationCatalog\Application\Resource\Interfaces\AdminApplicationResourceInterface;
use App\General\Transport\Rest\CrudController;
use OpenApi\Attributes as OA;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: '/v1/admin/applications')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Admin - Application Catalog Management')]
final class AdminApplicationController extends CrudController
{
    protected static string $dtoBaseClass = AdminApplication::class;

    public function __construct(AdminApplicationResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
