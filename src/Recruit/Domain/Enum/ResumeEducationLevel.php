<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum ResumeEducationLevel: string implements DatabaseEnumInterface
{
    use GetValues;

    case HIGH_SCHOOL = 'high_school';
    case ASSOCIATE = 'associate';
    case BACHELOR = 'bachelor';
    case MASTER = 'master';
    case DOCTORATE = 'doctorate';
    case OTHER = 'other';
}
