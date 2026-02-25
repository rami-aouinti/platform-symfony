<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Rest;

use App\ApiKey\Application\Security\ApiKeyUser;
use App\User\Application\Security\SecurityUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

use function array_key_exists;
use function hash;
use function is_array;
use function is_object;
use function is_scalar;
use function json_encode;
use function ksort;

class ReadEndpointCache
{
    /**
     * @param array<string, int> $ttls
     */
    public function __construct(
        private readonly TagAwareCacheInterface $cache,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly int $defaultTtl,
        private readonly array $ttls = [],
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function remember(string $scope, Request $request, array $context, callable $resolver): mixed
    {
        $key = $this->buildKey($scope, $request, $context);
        $tag = $this->buildTag($scope);

        return $this->cache->get($key, function (ItemInterface $item) use ($resolver, $tag, $scope): mixed {
            $item->tag($tag);
            $item->expiresAfter($this->resolveTtl($scope));

            return $resolver();
        });
    }

    public function invalidate(string $scope): void
    {
        $this->cache->invalidateTags([$this->buildTag($scope)]);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function buildKey(string $scope, Request $request, array $context): string
    {
        $payload = [
            'scope' => $scope,
            'route' => $request->attributes->get('_route', $request->getPathInfo()),
            'query' => [
                'criteria' => $context['criteria'] ?? null,
                'orderBy' => $context['orderBy'] ?? null,
                'limit' => $context['limit'] ?? null,
                'offset' => $context['offset'] ?? null,
                'search' => $context['search'] ?? null,
            ],
            'tenant' => $context['tenant'] ?? null,
            'user' => $this->resolveUserContext(),
        ];

        return 'read_endpoint:' . hash('sha256', (string) json_encode($this->normalize($payload), JSON_THROW_ON_ERROR));
    }

    private function buildTag(string $scope): string
    {
        return 'read_endpoint_scope:' . hash('sha256', $scope);
    }

    private function resolveTtl(string $scope): int
    {
        return array_key_exists($scope, $this->ttls) ? $this->ttls[$scope] : $this->defaultTtl;
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveUserContext(): array
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if ($user instanceof SecurityUser) {
            return [
                'type' => 'user',
                'id' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ];
        }

        if ($user instanceof ApiKeyUser) {
            return [
                'type' => 'api_key',
                'id' => $user->getApiKeyIdentifier(),
                'roles' => $user->getRoles(),
            ];
        }

        return [
            'type' => is_object($user) ? $user::class : 'anonymous',
            'id' => null,
            'roles' => [],
        ];
    }

    private function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            ksort($value);

            foreach ($value as $key => $innerValue) {
                $value[$key] = $this->normalize($innerValue);
            }

            return $value;
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        return (string) $value;
    }
}
