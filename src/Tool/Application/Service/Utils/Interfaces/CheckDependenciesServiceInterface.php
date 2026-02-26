<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Utils\Interfaces;

use Exception;

/**
 * @package App\Tool
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface CheckDependenciesServiceInterface
{
    /**
     * Method to determine all namespace directories under 'tools' directory.
     *
     * @throws Exception
     *
     * @return array<int, string>
     */
    public function getNamespaceDirectories(): array;
}
