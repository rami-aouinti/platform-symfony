<?php

declare(strict_types=1);

namespace App\Tests\Unit\General\Domain\Entity\Traits;

use App\General\Domain\Entity\Traits\ColorTrait;
use App\General\Domain\Entity\Traits\DescriptionTrait;
use App\General\Domain\Entity\Traits\NameTrait;
use App\General\Domain\Entity\Traits\SlugTrait;
use App\General\Domain\Entity\Traits\StatusTrait;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class SharedTraitsAccessorsTest extends TestCase
{
    #[TestDox('Shared traits expose strict typed getters and fluent setters.')]
    public function testSharedTraitsAccessors(): void
    {
        $entity = new class() {
            use NameTrait;
            use DescriptionTrait;
            use SlugTrait;
            use ColorTrait;
            use StatusTrait;
        };

        self::assertSame($entity, $entity->setName('Name'));
        self::assertSame($entity, $entity->setDescription('Description'));
        self::assertSame($entity, $entity->setSlug('my-slug'));
        self::assertSame($entity, $entity->setColor('#FFFFFF'));
        self::assertSame($entity, $entity->setStatus('active'));

        self::assertSame('Name', $entity->getName());
        self::assertSame('Description', $entity->getDescription());
        self::assertSame('my-slug', $entity->getSlug());
        self::assertSame('#FFFFFF', $entity->getColor());
        self::assertSame('active', $entity->getStatus());

        $entity->setColor(null);
        self::assertNull($entity->getColor());
    }
}
