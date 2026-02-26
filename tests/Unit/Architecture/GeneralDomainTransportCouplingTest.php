<?php

declare(strict_types=1);

namespace App\Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function file_get_contents;
use function str_contains;
use function str_ends_with;

class GeneralDomainTransportCouplingTest extends TestCase
{
    #[TestDox('General Domain stays transport-neutral and does not reference Serializer Groups.')]
    public function testGeneralDomainIsTransportNeutral(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__ . '/../../../src/General/Domain'),
        );

        $violations = [];

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getPathname();

            if (!str_ends_with($path, '.php')) {
                continue;
            }

            $content = file_get_contents($path);

            if ($content === false) {
                continue;
            }

            if (str_contains($content, 'Symfony\\Component\\Serializer\\Attribute\\Groups')) {
                $violations[] = $path . ' imports Groups attribute';
            }

            if (str_contains($content, '#[Groups(')) {
                $violations[] = $path . ' declares #[Groups(...)] attribute';
            }
        }

        self::assertSame([], $violations, "General Domain must remain transport-neutral.\n" . implode("\n", $violations));
    }
}
