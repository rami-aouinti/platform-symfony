<?php

declare(strict_types=1);

namespace App\Configuration\Transport\Controller\Api\V1\Configuration;

use App\Configuration\Application\DTO\Configuration\ConfigurationCreate;
use App\Configuration\Application\DTO\Configuration\ConfigurationPatch;
use App\Configuration\Application\DTO\Configuration\ConfigurationUpdate;
use App\Configuration\Application\Resource\ConfigurationResource;
use App\Configuration\Application\Resource\Interfaces\ConfigurationResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method ConfigurationResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/configuration')]
#[Route(path: '/v1/configurations')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Configuration Management')]
class ConfigurationController extends Controller
{
    use Actions\Authenticated\CountAction;
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\IdsAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ConfigurationCreate::class,
        Controller::METHOD_UPDATE => ConfigurationUpdate::class,
        Controller::METHOD_PATCH => ConfigurationPatch::class,
    ];

    public function __construct(
        ConfigurationResourceInterface $resource,
    ) {
        parent::__construct($resource);
    }
}
