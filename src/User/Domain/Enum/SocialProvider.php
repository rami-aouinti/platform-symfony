<?php

declare(strict_types=1);

namespace App\User\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

enum SocialProvider: string implements DatabaseEnumInterface
{
    use GetValues;

    case FACEBOOK = 'facebook';
    case GOOGLE = 'google';
    case INSTAGRAM = 'instagram';
    case GITHUB = 'github';
    case LINKEDIN = 'linkedin';
    case AZURE = 'azure';
    case GITLAB = 'gitlab';
}
