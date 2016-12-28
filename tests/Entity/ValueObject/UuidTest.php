<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Uuid;
use Trellis\Tests\TestCase;

final class UuidTest extends TestCase
{
    private const FIXED_UUID = "110ec58a-a0f2-4ac4-8393-c866d813b8d1";

    /**
     * @var Uuid $uuid
     */
    private $uuid;

    public function testToNative(): void
    {
        $this->assertEquals(Uuid::EMPTY, (new Uuid)->toNative());
        $this->assertEquals(self::FIXED_UUID, $this->uuid->toNative());
    }

    public function testEquals(): void
    {
        $this->assertTrue($this->uuid->equals(new Uuid(self::FIXED_UUID)));
        $this->assertFalse($this->uuid->equals(Uuid::generate()));
        $this->assertFalse($this->uuid->equals(new Uuid));
    }

    public function testIsEmpty(): void
    {
        $this->assertFalse($this->uuid->isEmpty());
        $this->assertTrue((new Uuid)->isEmpty());
    }

    public function testString(): void
    {
        $this->assertEquals(self::FIXED_UUID, (string)$this->uuid);
    }

    protected function setUp(): void
    {
        $this->uuid = new Uuid(self::FIXED_UUID);
    }
}
