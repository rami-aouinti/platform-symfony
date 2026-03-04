<?php

declare(strict_types=1);

namespace App\Page\Transport\Controller\Api\V1\Contact;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use App\Page\Application\DTO\Contact\ContactCreate;
use App\Page\Application\DTO\Contact\ContactPatch;
use App\Page\Application\DTO\Contact\ContactUpdate;
use App\Page\Application\Resource\Interfaces\ContactResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/v1/admin/pages/contacts')]
#[OA\Tag(name: 'Admin - Page')]
class AdminContactController extends Controller
{
    use Actions\Root\CreateAction;
    use Actions\Root\DeleteAction;
    use Actions\Root\FindAction;
    use Actions\Root\FindOneAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;
    use Actions\Root\SchemaAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ContactCreate::class,
        Controller::METHOD_UPDATE => ContactUpdate::class,
        Controller::METHOD_PATCH => ContactPatch::class,
    ];

    public function __construct(ContactResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
