<?php

declare(strict_types=1);

namespace App\Recruit\Transport\Controller\Api\V1\Catalog;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use App\Recruit\Application\Resource\Interfaces\SkillResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: '/api/v1/catalog/skills')]
#[Route(path: '/v1/catalog/skills')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Catalog Skill')]
class SkillCatalogController extends Controller
{
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\SchemaAction;

    public function __construct(SkillResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
