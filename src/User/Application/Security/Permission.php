<?php

declare(strict_types=1);

namespace App\User\Application\Security;

enum Permission: string
{
    case OFFER_VIEW = 'offer.view';
    case OFFER_MANAGE = 'offer.manage';

    case APPLICATION_VIEW = 'application.view';
    case APPLICATION_MANAGE = 'application.manage';
    case APPLICATION_DECIDE = 'application.decide';
    case APPLICATION_WITHDRAW = 'application.withdraw';

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
}
