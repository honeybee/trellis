<?php

namespace Trellis\Tests\ValueObject;

use Trellis\Tests\TestCase;
use Trellis\ValueObject\Uuid;

final class UuidTest extends TestCase
{
    private const FIXED_UUID = "110ec58a-a0f2-4ac4-8393-c866d813b8d1";

    /**
     * @var Uuid $uuid
     */
    private $uuid;

    public function testToNative(): void
    {
        $this->assertEquals(null, Uuid::makeEmpty()->toNative());
        $this->assertEquals(self::FIXED_UUID, $this->uuid->toNative());
    }

    public function testEquals(): void
    {
        $this->assertTrue($this->uuid->equals(Uuid::fromNative(self::FIXED_UUID)));
        $this->assertFalse($this->uuid->equals(Uuid::generate()));
        $this->assertFalse($this->uuid->equals(Uuid::makeEmpty()));
    }

    public function testIsEmpty(): void
    {
        $this->assertFalse($this->uuid->isEmpty());
        $this->assertTrue(Uuid::makeEmpty()->isEmpty());
    }

    public function testString(): void
    {
        $this->assertEquals(self::FIXED_UUID, (string)$this->uuid);
    }

    protected function setUp(): void
    {
        $this->uuid = Uuid::fromNative(self::FIXED_UUID);
    }
}
