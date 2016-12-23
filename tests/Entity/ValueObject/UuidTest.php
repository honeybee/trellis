<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Uuid;
use Trellis\Tests\TestCase;

final class UuidTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertEquals(Uuid::EMPTY, (new Uuid)->toNative());
        $this->assertEquals(
            "110ec58a-a0f2-4ac4-8393-c866d813b8d1",
            (new Uuid("110ec58a-a0f2-4ac4-8393-c866d813b8d1"))->toNative()
        );
    }

    public function testEquals(): void
    {
        $uuid = Uuid::generate();
        $this->assertTrue($uuid->equals(new Uuid($uuid->toNative())));
        $this->assertFalse($uuid->equals(Uuid::generate()));
    }
}
