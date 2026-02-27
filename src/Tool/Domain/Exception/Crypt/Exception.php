<?php

declare(strict_types=1);

namespace App\Tool\Domain\Exception\Crypt;

use App\General\Domain\Exception\BaseTranslatableException;

/**
 * @package App\Tool\Domain\Exception\Crypt
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class Exception extends BaseTranslatableException
{
    public function getDomain(): ?string
    {
        return 'crypt';
    }
}
