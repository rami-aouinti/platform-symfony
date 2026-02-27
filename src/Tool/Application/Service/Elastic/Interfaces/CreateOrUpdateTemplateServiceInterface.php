<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Elastic\Interfaces;

use Throwable;

/**
 * @package App\Tool\Application\Service\Elastic\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface CreateOrUpdateTemplateServiceInterface
{
    /**
     * Create/update elastic template
     *
     * @throws Throwable
     */
    public function createOrUpdateIndexTemplate(): string;
}
