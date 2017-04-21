<?php

namespace Trellis\Tests\ValueObject;

use Trellis\Tests\TestCase;
use Trellis\ValueObject\Integer;

final class IntegerTest extends TestCase
{
    private const FIXED_NUM = 23;

    /**
     * @var Integer
     */
    private $integer;

    public function testToNative(): void
    {
        $this->assertEquals(self::FIXED_NUM, $this->integer->toNative());
        $this->assertNull(Integer::makeEmpty()->toNative());
    }

    public function testEquals(): void
    {
        $sameNumber = Integer::fromNative(self::FIXED_NUM);
        $this->assertTrue($this->integer->equals($sameNumber));
        $differentNumber = Integer::fromNative(42);
        $this->assertFalse($this->integer->equals($differentNumber));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue(Integer::makeEmpty()->isEmpty());
        $this->assertFalse($this->integer->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals((string)self::FIXED_NUM, (string)$this->integer);
    }

    protected function setUp()
    {
        $this->integer = Integer::fromNative(self::FIXED_NUM);
    }
}
