<?php

declare(strict_types=1);

namespace App\Media\Transport\Controller\Api\V1\Media;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Media\Application\DTO\Media\MediaCreate;
use App\Media\Application\DTO\Media\MediaPatch;
use App\Media\Application\DTO\Media\MediaUpdate;
use App\Media\Application\Resource\Interfaces\MediaResourceInterface;
use App\Media\Application\Resource\MediaResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method MediaResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/media')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Media Management')]
class MediaController extends Controller
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
        Controller::METHOD_CREATE => MediaCreate::class,
        Controller::METHOD_UPDATE => MediaUpdate::class,
        Controller::METHOD_PATCH => MediaPatch::class,
    ];

    public function __construct(MediaResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
