<?php

declare(strict_types=1);

namespace App\Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function file_get_contents;
use function preg_match;
use function preg_match_all;
use function sprintf;
use function str_contains;
use function str_ends_with;

class DtoClassesScopeConventionTest extends TestCase
{
    #[TestDox('Controllers declaring dtoClasses are limited to admin or user/profile route scopes.')]
    public function testDtoClassesScopeConvention(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__ . '/../../../src'),
        );

        $violations = [];

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getPathname();

            if (!str_ends_with($path, 'Controller.php')) {
                continue;
            }

            if (!str_contains($path, '/Transport/Controller/')) {
                continue;
            }

            $content = file_get_contents($path);

            if ($content === false || !str_contains($content, 'protected static array $dtoClasses')) {
                continue;
            }

            preg_match('/class\s+(\w+Controller)\s+extends/', $content, $classMatch);
            $className = $classMatch[1] ?? 'UnknownController';

            preg_match_all("/#\[Route\(path:\s*'([^']+)'/", $content, $routeMatches);
            $routes = $routeMatches[1] ?? [];

            $hasAdminRoute = false;
            $hasMeRoute = false;
            $hasMeProfileRoute = false;

            foreach ($routes as $route) {
                if (str_contains($route, '/admin/')) {
                    $hasAdminRoute = true;
                }

                if (str_contains($route, '/me/')) {
                    $hasMeRoute = true;
                }

                if (str_contains($route, '/me/profile/')) {
                    $hasMeProfileRoute = true;
                }
            }

            if (!$hasAdminRoute && !$hasMeRoute) {
                $violations[] = sprintf(
                    '%s (%s): dtoClasses requires route scope /admin/ or /me/.',
                    $className,
                    $path,
                );
            }

            if ($hasMeProfileRoute && !str_starts_with($className, 'Profile')) {
                $violations[] = sprintf(
                    '%s (%s): /me/profile/ routes require a Profile*Controller class name.',
                    $className,
                    $path,
                );
            }

            if (str_starts_with($className, 'Profile') && !$hasMeProfileRoute) {
                $violations[] = sprintf(
                    '%s (%s): Profile*Controller with dtoClasses must expose a /me/profile/ route.',
                    $className,
                    $path,
                );
            }
        }

        self::assertSame([], $violations, "dtoClasses route scope convention violated:\n" . implode("\n", $violations));
    }
}
