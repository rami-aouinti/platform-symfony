<?php

declare(strict_types=1);

namespace App\Tests\Unit\General\Application\DTO;

use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use PHPUnit\Framework\TestCase;

class RestDtoTest extends TestCase
{
    public function testPatchSupportsGetterMethodMatchingPropertyName(): void
    {
        $target = new TestRestDto();
        $patch = (new TestRestDto())->setIsPublic(true);

        $target->patch($patch);

        self::assertTrue($target->isPublic());
    }
}

class TestRestDto extends RestDto
{
    private bool $isPublic = false;

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->setVisited('isPublic');
        $this->isPublic = $isPublic;

        return $this;
    }

    public function load(EntityInterface $entity): self
    {
        return $this;
    }
}
