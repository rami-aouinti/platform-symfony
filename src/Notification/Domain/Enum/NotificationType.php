<?php

declare(strict_types=1);

namespace App\Notification\Domain\Enum;

enum NotificationType: string
{
    case COMPANY_CREATED = 'company_created';
    case JOB_APPLICATION_SUBMITTED = 'job_application_submitted';
    case JOB_APPLICATION_DECIDED = 'job_application_decided';
}

