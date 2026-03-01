<?php

declare(strict_types=1);

namespace App\Company\Domain\Repository\Interfaces;

use App\Company\Domain\Entity\Company;

/**
 * @package App\Company\Domain\Repository\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface CompanyRepositoryInterface
{
    public function find(string $id): ?Company;
}
