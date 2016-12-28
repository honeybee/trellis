<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Integer;
use Trellis\Tests\TestCase;

final class IntegerTest extends TestCase
{
    private const FIXED_NUM = 23;

    /**
     * @var Integer $integer
     */
    private $integer;

    public function testToNative(): void
    {
        $this->assertEquals(self::FIXED_NUM, $this->integer->toNative());
        $this->assertEquals(Integer::EMPTY, (new Integer)->toNative());
    }

    public function testEquals(): void
    {
        $same_number = new Integer(self::FIXED_NUM);
        $this->assertTrue($this->integer->equals($same_number));
        $different_number = new Integer(42);
        $this->assertFalse($this->integer->equals($different_number));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new Integer)->isEmpty());
        $this->assertFalse($this->integer->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals((string)self::FIXED_NUM, (string)$this->integer);
    }

    protected function setUp()
    {
        $this->integer = new Integer(self::FIXED_NUM);
    }
}
