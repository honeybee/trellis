<?php

namespace Trellis\Tests\ValueObject;

use Trellis\Tests\TestCase;
use Trellis\ValueObject\Boolean;

final class BooleanTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertTrue(Boolean::fromNative(true)->toNative());
        $this->assertFalse(Boolean::fromNative(false)->toNative());
        $this->assertFalse(Boolean::makeEmpty()->toNative());
    }

    public function testEquals(): void
    {
        $bool = Boolean::fromNative(true);
        $this->assertTrue($bool->equals(Boolean::fromNative(true)));
        $this->assertFalse($bool->equals(Boolean::fromNative(false)));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue(Boolean::makeEmpty()->isEmpty());
        $this->assertFalse(Boolean::fromNative(true)->isEmpty());
    }

    public function testIsTrue(): void
    {
        $this->assertTrue(Boolean::fromNative(true)->isTrue());
    }

    public function testIsFalse(): void
    {
        $this->assertTrue(Boolean::fromNative(false)->isFalse());
    }

    public function testNegate(): void
    {
        $this->assertTrue(Boolean::fromNative(false)->negate()->toNative());
    }

    public function testToString(): void
    {
        $this->assertEquals("true", (string)Boolean::fromNative(true));
        $this->assertEquals("false", (string)Boolean::fromNative(false));
    }
}
