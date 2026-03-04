<?php

declare(strict_types=1);

namespace App\Page\Transport\Controller\Api\V1\About;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use App\Page\Application\DTO\About\AboutCreate;
use App\Page\Application\DTO\About\AboutPatch;
use App\Page\Application\DTO\About\AboutUpdate;
use App\Page\Application\Resource\Interfaces\AboutResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/v1/admin/pages/abouts')]
#[OA\Tag(name: 'Admin - Page')]
class AdminAboutController extends Controller
{
    use Actions\Root\CreateAction;
    use Actions\Root\DeleteAction;
    use Actions\Root\FindAction;
    use Actions\Root\FindOneAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;
    use Actions\Root\SchemaAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => AboutCreate::class,
        Controller::METHOD_UPDATE => AboutUpdate::class,
        Controller::METHOD_PATCH => AboutPatch::class,
    ];

    public function __construct(AboutResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
