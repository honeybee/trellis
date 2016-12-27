<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Decimal;
use Trellis\Tests\TestCase;

final class DecimalTest extends TestCase
{
    const FIXED_DEC = 2.3;

    /**
     * @var Decimal $decimal;
     */
    private $decimal;

    public function testToNative(): void
    {
        $this->assertEquals(self::FIXED_DEC, $this->decimal->toNative());
        $this->assertEquals(Decimal::EMPTY, (new Decimal)->toNative());
    }

    public function testEquals(): void
    {
        $same_number = new Decimal(self::FIXED_DEC);
        $this->assertTrue($this->decimal->equals($same_number));
        $different_number = new Decimal(4.2);
        $this->assertFalse($this->decimal->equals($different_number));
    }

    public function testIsEmpty(): void
    {
        $this->assertFalse($this->decimal->isEmpty());
        $this->assertTrue((new Decimal)->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals((string)self::FIXED_DEC, (string)$this->decimal);
        $this->assertEquals("null", (string)new Decimal);
    }

    protected function setUp(): void
    {
        $this->decimal = new Decimal(self::FIXED_DEC);
    }
}
