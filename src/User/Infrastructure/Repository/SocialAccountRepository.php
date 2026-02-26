<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\User\Domain\Entity\SocialAccount;
use App\User\Domain\Enum\SocialProvider;
use App\User\Domain\Repository\Interfaces\SocialAccountRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @package App\User
 *
 * @method SocialAccount|null find(string $id, \Doctrine\DBAL\LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method SocialAccount|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class SocialAccountRepository extends BaseRepository implements SocialAccountRepositoryInterface
{
    protected static string $entityName = SocialAccount::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry
    ) {
    }

    public function findOneByProviderAndExternalId(SocialProvider $provider, string $providerUserId): ?SocialAccount
    {
        /** @var SocialAccount|null $account */
        $account = $this->findOneBy([
            'provider' => $provider,
            'providerUserId' => $providerUserId,
        ]);

        return $account;
    }
}
