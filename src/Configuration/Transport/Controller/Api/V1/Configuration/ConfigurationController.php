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
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for ConfigurationController endpoints.
 *
 * @method ConfigurationResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Configuration\Transport\Controller\Api\V1\Configuration
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/api/v1/admin/configuration')]
#[Route(path: '/v1/configuration')]
#[Route(path: '/api/v1/admin/configurations')]
#[Route(path: '/v1/configurations')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
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
    use Actions\Authenticated\SchemaAction;

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
