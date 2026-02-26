<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Security\Provider\Interfaces;

use App\ApiKey\Domain\Entity\ApiKey;
use App\ApiKey\Domain\Repository\Interfaces\ApiKeyRepositoryInterface;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use Throwable;

/**
 * @package App\ApiKey
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface ApiKeyUserProviderInterface
{
    public function __construct(
        bool $apiKeyTokenOpenSslEncrypt,
        string $apiKeyTokenHashAlgo,
        ApiKeyRepositoryInterface $apiKeyRepository,
        RolesServiceInterface $rolesService
    );

    /**
     * Method to fetch ApiKey entity for specified token.
     *
     * @throws Throwable
     */
    public function getApiKeyForToken(string $token): ?ApiKey;
}
