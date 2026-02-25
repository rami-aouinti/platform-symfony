<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tool\Application\Service\Rest;

use App\Tool\Application\Service\Rest\ReadEndpointCache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReadEndpointCacheTest extends TestCase
{
    public function testRememberUsesCacheKeyCompatibleWithSymfonyCache(): void
    {
        $cache = new ArrayAdapter();
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $readEndpointCache = new ReadEndpointCache($cache, $tokenStorage, 60);

        $request = Request::create('/api/v1/job-offers?status=open');
        $request->attributes->set('_route', 'api_v1_job_offer_find');

        $result = $readEndpointCache->remember('job_offer', $request, [
            'criteria' => ['status' => 'open'],
            'orderBy' => ['publishedAt' => 'DESC'],
            'limit' => 20,
            'offset' => 0,
            'search' => [],
        ], static fn (): array => ['ok' => true]);

        self::assertSame(['ok' => true], $result);
    }
}
