<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum ResumeEmploymentType: string implements DatabaseEnumInterface
{
    use GetValues;

    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case FREELANCE = 'freelance';
    case CONTRACT = 'contract';
    case INTERNSHIP = 'internship';
}
