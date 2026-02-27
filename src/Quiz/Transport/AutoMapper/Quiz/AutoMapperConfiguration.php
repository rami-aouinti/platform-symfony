<?php

declare(strict_types=1);

namespace App\Quiz\Transport\AutoMapper\Quiz;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Quiz\Application\DTO\Quiz\QuizCreate;
use App\Quiz\Application\DTO\Quiz\QuizPatch;
use App\Quiz\Application\DTO\Quiz\QuizUpdate;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Quiz\Transport\AutoMapper\Quiz
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        QuizCreate::class,
        QuizUpdate::class,
        QuizPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
