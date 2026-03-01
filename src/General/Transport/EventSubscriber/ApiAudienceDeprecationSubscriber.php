<?php

declare(strict_types=1);

namespace App\General\Transport\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiAudienceDeprecationSubscriber implements EventSubscriberInterface
{
    private const DEPRECATION_START = '2026-04-01';
    private const SUNSET_DATE = '2026-10-01';

    /**
     * @var string[]
     */
    private const MODULE_PREFIXES = [
        'api_key',
        'blog',
        'calendar',
        'chat',
        'companies',
        'company',
        'configuration',
        'configurations',
        'media',
        'notifications',
        'quizzes',
        'job-offers',
        'job-applications',
        'offers',
        'candidates',
        'resumes',
        'resume-education',
        'resume-skills',
        'resume-experiences',
        'role',
        'statistics',
        'tasks',
        'task-requests',
        'sprints',
        'projects',
        'user',
        'user_group',
        'profile',
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $path = trim($event->getRequest()->getPathInfo(), '/');

        if (!str_starts_with($path, 'api/v1/')) {
            return;
        }

        if (str_starts_with($path, 'api/v1/admin/') || str_starts_with($path, 'api/v1/me/')) {
            return;
        }

        $withoutVersion = substr($path, strlen('api/v1/'));
        $firstSegment = explode('/', $withoutVersion)[0] ?? '';

        if (!in_array($firstSegment, self::MODULE_PREFIXES, true)) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set(
            'Deprecation',
            sprintf('version="%s", message="Legacy /api/v1/* audience-agnostic endpoints are deprecated; migrate to /api/v1/admin/* or /api/v1/me/*"', self::DEPRECATION_START)
        );
        $response->headers->set('Sunset', gmdate(DATE_RFC7231, strtotime(self::SUNSET_DATE . ' 00:00:00 UTC')));
        $response->headers->set('Link', '</docs/api-audience-segmentation.md>; rel="deprecation"');
    }
}
