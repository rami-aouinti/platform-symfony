<?php

declare(strict_types=1);

namespace App\Offer\Transport\Controller\Api\V1\Offer;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Offer\Application\DTO\Offer\OfferCreate;
use App\Offer\Application\DTO\Offer\OfferPatch;
use App\Offer\Application\DTO\Offer\OfferUpdate;
use App\Offer\Application\Resource\Interfaces\OfferResourceInterface;
use App\Offer\Application\Resource\OfferResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method OfferResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Offer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/v1/offers')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Offer Management')]
class OfferController extends Controller
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
        Controller::METHOD_CREATE => OfferCreate::class,
        Controller::METHOD_UPDATE => OfferUpdate::class,
        Controller::METHOD_PATCH => OfferPatch::class,
    ];

    public function __construct(OfferResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
