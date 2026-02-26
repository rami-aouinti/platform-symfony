<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Interfaces;

/**
 * @package App\General
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface RestResourceInterface extends
    BaseRestResourceInterface,
    RestCountResourceInterface,
    RestCreateResourceInterface,
    RestDeleteResourceInterface,
    RestIdsResourceInterface,
    RestListResourceInterface,
    RestPatchResourceInterface,
    RestUpdateResourceInterface,
    RestFindOneResourceInterface
{
}
