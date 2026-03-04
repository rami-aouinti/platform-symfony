<?php

declare(strict_types=1);

namespace App\Media\Transport\Controller\Api\V1\Media;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\CrudController;
use App\General\Transport\Rest\ResponseHandler;
use App\Media\Application\DTO\Media\Media;
use App\Media\Application\Resource\Interfaces\MediaResourceInterface;
use App\Media\Application\Resource\MediaResource;
use App\Media\Application\Service\MediaExportService;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

use function in_array;
use function is_file;
use function is_string;
use function str_starts_with;
use function trim;

/**
 * @method MediaResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/me/media')]
#[IsGranted('ROLE_USER')]
#[OA\Tag(name: 'Me - Media Management')]
class MeMediaController extends CrudController
{
    protected static string $dtoBaseClass = Media::class;

    public function __construct(
        MediaResourceInterface $resource,
        private readonly ValidatorInterface $validator,
        private readonly MediaExportService $mediaExportService,
    ) {
        parent::__construct($resource);
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '', methods: [Request::METHOD_POST])]
    #[OA\Post(
        operationId: 'meMediaCreate',
        summary: 'Créer un média',
        description: "Création d'un média pour le module Media.",
        security: [[
            'Bearer' => [],
        ], [
            'ApiKey' => [],
        ]],
    )]
    #[OA\RequestBody(
        request: 'meMediaCreateBody',
        description: 'Exemple de payload de création',
        content: new JsonContent(
            type: 'object',
            example: [
                'name' => 'Example name',
                'description' => 'Description initiale',
            ],
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'created',
        content: new JsonContent(
            type: 'object',
            example: [],
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }

    #[Route(path: '/upload', methods: [Request::METHOD_POST])]
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

        $folderId = $this->resolveFolderId($request);

        if ($folderId !== null) {
            $folderViolations = $this->validator->validate($folderId, [
                new Assert\Uuid(message: 'Invalid "folderId" format.'),
            ]);

            if ($folderViolations->count() > 0) {
                return $this->createValidationError((string)$folderViolations->get(0)->getMessage());
            }
        }

        $media = $this->getResource()->createFromUploadedFile($file, $folderId);

        return $this->getResponseHandler()->createResponse($request, $media, $this->getResource());
    }

    #[Route(path: '/export/{configurationId}/excel', methods: [Request::METHOD_GET])]
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


    #[Route(path: '/{id}/download', methods: [Request::METHOD_GET])]
    public function downloadAction(string $id): Response
    {
        $media = $this->getResource()->findOneAccessible($id);
        $filePath = $this->getResource()->getStorageService()->resolveFilesystemPath($media->getPath());

        if (!is_file($filePath)) {
            return $this->json([
                'message' => 'Media file not found on storage.',
            ], Response::HTTP_NOT_FOUND);
        }

        $response = new BinaryFileResponse($filePath);
        $disposition = HeaderUtils::makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $media->getName(),
        );

        $response->headers->set('Content-Type', $media->getMimeType());
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    #[Route(path: '/{id}/view', methods: [Request::METHOD_GET])]
    public function viewAction(string $id): Response
    {
        $media = $this->getResource()->findOneAccessible($id);
        $filePath = $this->getResource()->getStorageService()->resolveFilesystemPath($media->getPath());

        if (!is_file($filePath)) {
            return $this->json([
                'message' => 'Media file not found on storage.',
            ], Response::HTTP_NOT_FOUND);
        }

        $inlineMimeTypes = ['application/pdf'];
        $dispositionType =
            str_starts_with($media->getMimeType(), 'image/') || in_array($media->getMimeType(), $inlineMimeTypes, true)
                ? ResponseHeaderBag::DISPOSITION_INLINE
                : ResponseHeaderBag::DISPOSITION_ATTACHMENT;

        $response = new BinaryFileResponse($filePath);
        $disposition = HeaderUtils::makeDisposition($dispositionType, $media->getName());

        $response->headers->set('Content-Type', $media->getMimeType());
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }


    private function resolveFolderId(Request $request): ?string
    {
        $folderId = $request->request->get('folderId');

        if (!is_string($folderId)) {
            return null;
        }

        $folderId = trim($folderId);

        return $folderId !== '' ? $folderId : null;
    }

    private function createValidationError(string $message): Response
    {
        return $this->json([
            'message' => $message,
        ], Response::HTTP_BAD_REQUEST);
    }
}
