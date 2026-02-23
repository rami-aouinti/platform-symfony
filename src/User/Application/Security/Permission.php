<?php

declare(strict_types=1);

namespace App\User\Application\Security;

enum Permission: string
{
    case BLOG_VIEW = 'blog.view';
    case BLOG_MANAGE = 'blog.manage';

    case CRM_VIEW = 'crm.view';
    case CRM_MANAGE = 'crm.manage';

    case SHOP_VIEW = 'shop.view';
    case SHOP_MANAGE = 'shop.manage';

    case EDUCATION_VIEW = 'education.view';
    case EDUCATION_MANAGE = 'education.manage';
}
