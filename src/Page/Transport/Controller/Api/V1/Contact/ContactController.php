<?php

declare(strict_types=1);

namespace App\Page\Transport\Controller\Api\V1\Contact;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use App\Page\Application\Resource\Interfaces\ContactResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/v1/pages/contacts')]
#[OA\Tag(name: 'Page')]
class ContactController extends Controller
{
    use Actions\Anon\FindAction;
    use Actions\Anon\FindOneAction;
    use Actions\Anon\SchemaAction;

    public function __construct(ContactResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
