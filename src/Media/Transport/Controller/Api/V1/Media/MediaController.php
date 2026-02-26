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
use App\Media\Application\Service\MediaExportService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method MediaResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Media
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
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

    public function __construct(
        MediaResourceInterface $resource,
        private readonly ValidatorInterface $validator,
        private readonly MediaExportService $mediaExportService,
    ) {
        parent::__construct($resource);
    }

    #[Route(path: '/upload', methods: [Request::METHOD_POST])]
    #[IsGranted('ROLE_USER')]
    public function uploadAction(Request $request): Response
    {
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            return $this->createValidationError('Missing required file parameter "file".');
        }

        $violations = $this->validator->validate($file, [
            new Assert\File(
                maxSize: '10M',
                maxSizeMessage: 'Uploaded file is too large. Maximum allowed size is 10MB.',
                mimeTypesMessage: 'Provided file type is not supported.',
            ),
        ]);

        if ($violations->count() > 0) {
            return $this->createValidationError((string)$violations->get(0)->getMessage());
        }

        $media = $this->getResource()->createFromUploadedFile($file);

        return $this->getResponseHandler()->createResponse($request, $media, $this->getResource());
    }

    #[Route(path: '/export/{configurationId}/excel', methods: [Request::METHOD_GET])]
    #[IsGranted('ROLE_USER')]
    public function exportExcelAction(string $configurationId): Response
    {
        $configuration = $this->getResource()->resolveExportConfiguration($configurationId);
        $mediaItems = $this->getResource()->findForExport($configuration['status']);
        $content = $this->mediaExportService->buildExcelContent($mediaItems, $configuration['columns']);

        return new Response(
            $content,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="media-export.xls"',
            ],
        );
    }

    #[Route(path: '/export/{configurationId}/pdf', methods: [Request::METHOD_GET])]
    #[IsGranted('ROLE_USER')]
    public function exportPdfAction(string $configurationId): Response
    {
        $configuration = $this->getResource()->resolveExportConfiguration($configurationId);
        $mediaItems = $this->getResource()->findForExport($configuration['status']);
        $content = $this->mediaExportService->buildPdfContent($mediaItems, $configuration['columns'], $configuration['title']);

        return new Response(
            $content,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="media-export.pdf"',
            ],
        );
    }

    private function createValidationError(string $message): Response
    {
        return $this->json([
            'message' => $message,
        ], Response::HTTP_BAD_REQUEST);
    }
}
