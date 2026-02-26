<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Security\Interfaces;

use App\ApiKey\Domain\Entity\ApiKey;

/**
 * @package App\ApiKey
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface ApiKeyUserInterface
{
    /**
     * @param array<int, string> $roles
     */
    public function __construct(ApiKey $apiKey, array $roles);
}
