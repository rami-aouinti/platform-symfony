<?php

declare(strict_types=1);

namespace App\User\Application\Security;

/**
 * @package App\User\Application\Security
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum Permission: string
{
    case RESUME_CREATE = 'resume.create';
    case RESUME_VIEW = 'resume.view';
    case RESUME_EDIT = 'resume.edit';
    case RESUME_DELETE = 'resume.delete';
    case RESUME_USE_FOR_APPLICATION = 'resume.use_for_application';

    case JOB_OFFER_VIEW = 'job_offer.view';
    case JOB_OFFER_MANAGE = 'job_offer.manage';

    case JOB_APPLICATION_VIEW = 'job_application.view';
    case JOB_APPLICATION_APPLY = 'job_application.apply';
    case JOB_APPLICATION_DECIDE = 'job_application.decide';
    case JOB_APPLICATION_WITHDRAW = 'job_application.withdraw';

    case BLOG_VIEW = 'blog.view';
    case BLOG_MANAGE = 'blog.manage';

    case CRM_VIEW = 'crm.view';
    case CRM_MANAGE = 'crm.manage';

    case SHOP_VIEW = 'shop.view';
    case SHOP_MANAGE = 'shop.manage';

    case EDUCATION_VIEW = 'education.view';
    case EDUCATION_MANAGE = 'education.manage';

    case NOTIFICATION_VIEW = 'notification.view';
    case NOTIFICATION_MANAGE = 'notification.manage';

    case CHAT_VIEW = 'chat.view';
    case CHAT_POST = 'chat.post';
    case CHAT_EDIT = 'chat.edit';
    case CHAT_DELETE = 'chat.delete';
    case CHAT_PARTICIPANT_MANAGE = 'chat.participant.manage';
    case CHAT_MODERATE = 'chat.moderate';
}
