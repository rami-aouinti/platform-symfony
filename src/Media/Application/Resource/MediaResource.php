<?php

declare(strict_types=1);

namespace App\Media\Application\Resource;

use App\Configuration\Domain\Entity\Configuration;
use App\Configuration\Domain\Repository\Interfaces\ConfigurationRepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Media\Application\Resource\Interfaces\MediaResourceInterface;
use App\Media\Application\Service\Interfaces\MediaStorageServiceInterface;
use App\Media\Domain\Entity\Media as Entity;
use App\Media\Domain\Enum\MediaStatus;
use App\Media\Domain\Repository\Interfaces\MediaRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function in_array;
use function is_array;
use function is_string;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Media\Application\Resource
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class MediaResource extends RestResource implements MediaResourceInterface
{
    /**
     * @var string[]
     */
    private const ALLOWED_EXPORT_COLUMNS = ['id', 'name', 'path', 'mimeType', 'size', 'status', 'ownerId', 'createdAt'];

    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly MediaStorageServiceInterface $mediaStorageService,
        private readonly ConfigurationRepositoryInterface $configurationRepository,
    ) {
        parent::__construct($repository);
    }

    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->isAdminLike($currentUser)) {
            return;
        }

        $criteria['owner'] = $currentUser;
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageMedia($entity);
        }
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity) {
            return;
        }

        $entity->setOwner($this->getCurrentUser());
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageMedia($entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageMedia($entity);
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity) {
            return;
        }

        $this->assertCanManageMedia($entity);
        $this->mediaStorageService->delete($entity->getPath());
    }

    public function createFromUploadedFile(UploadedFile $file): Entity
    {
        $owner = $this->getCurrentUser();
        $storedFile = $this->mediaStorageService->store($file, $owner->getId());

        $media = (new Entity())
            ->setOwner($owner)
            ->setName($storedFile->getOriginalName())
            ->setPath($storedFile->getPath())
            ->setMimeType($storedFile->getMimeType())
            ->setSize($storedFile->getSize())
            ->setStatus(MediaStatus::ACTIVE);

        $this->save($media);

        return $media;
    }

    public function findForExport(?string $status = null): array
    {
        $criteria = [];

        if ($status !== null && $status !== '') {
            $criteria['status'] = MediaStatus::from($status);
        }

        return $this->find(
            criteria: $criteria,
            orderBy: [
                'createdAt' => 'DESC',
            ],
        );
    }

    public function resolveExportConfiguration(string $configurationId): array
    {
        $configuration = $this->configurationRepository->find($configurationId);

        if (!$configuration instanceof Configuration) {
            throw new NotFoundHttpException('Export configuration not found.');
        }

        $decoded = json_decode($configuration->getValue(), true);

        if (!is_array($decoded)) {
            return [
                'columns' => ['id', 'name', 'mimeType', 'size', 'status', 'createdAt'],
                'status' => null,
                'title' => 'Media export',
            ];
        }

        $columns = array_values(array_filter(
            $decoded['columns'] ?? [],
            static fn (mixed $column): bool => is_string($column) && in_array($column, self::ALLOWED_EXPORT_COLUMNS, true),
        ));

        if ($columns === []) {
            $columns = ['id', 'name', 'mimeType', 'size', 'status', 'createdAt'];
        }

        $status = $decoded['status'] ?? null;

        if (!is_string($status) || $status === '') {
            $status = null;
        }

        $title = $decoded['title'] ?? 'Media export';

        if (!is_string($title) || $title === '') {
            $title = 'Media export';
        }

        return [
            'columns' => $columns,
            'status' => $status,
            'title' => $title,
        ];
    }

    private function assertCanManageMedia(Entity $media): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->isAdminLike($currentUser) || $media->getOwner()?->getId() === $currentUser->getId()) {
            return;
        }

        throw new AccessDeniedHttpException('Only media owner can manage this media.');
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }

    private function isAdminLike(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
