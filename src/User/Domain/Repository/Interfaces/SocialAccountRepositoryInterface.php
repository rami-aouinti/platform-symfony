<?php

declare(strict_types=1);

namespace App\User\Domain\Repository\Interfaces;

use App\User\Domain\Entity\SocialAccount;
use App\User\Domain\Enum\SocialProvider;

interface SocialAccountRepositoryInterface
{
    public function findOneByProviderAndExternalId(SocialProvider $provider, string $providerUserId): ?SocialAccount;
}
