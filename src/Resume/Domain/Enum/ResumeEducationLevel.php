<?php

declare(strict_types=1);

namespace App\Resume\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

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
