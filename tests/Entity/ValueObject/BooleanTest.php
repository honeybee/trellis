<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Boolean;
use Trellis\Tests\TestCase;

final class BooleanTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertTrue((new Boolean(true))->toNative());
        $this->assertFalse((new Boolean(false))->toNative());
        $this->assertEquals(Boolean::EMPTY, (new Boolean)->toNative());
    }

    public function testEquals(): void
    {
        $bool = new Boolean(true);
        $this->assertTrue($bool->equals(new Boolean(true)));
        $this->assertFalse($bool->equals(new Boolean(false)));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new Boolean)->isEmpty());
        $this->assertFalse((new Boolean(true))->isEmpty());
    }

    public function testIsTrue(): void
    {
        $this->assertTrue((new Boolean(true))->isTrue());
    }

    public function testIsFalse(): void
    {
        $this->assertTrue((new Boolean(false))->isFalse());
    }

    public function testNegate(): void
    {
        $this->assertTrue((new Boolean(false))->negate()->toNative());
    }

    public function testToString(): void
    {
        $this->assertEquals("true", (string)new Boolean(true));
        $this->assertEquals("false", (string)new Boolean(false));
    }
}
