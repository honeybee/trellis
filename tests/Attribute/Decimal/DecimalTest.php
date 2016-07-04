<?php

namespace Trellis\Tests\Attribute\Decimal;

use Trellis\Attribute\Decimal\Decimal;
use Trellis\Tests\TestCase;
use Trellis\Value\ValueInterface;

class DecimalTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Decimal(42.5));
    }

    public function testToNative()
    {
        $decimal = new Decimal(5.42);
        $this->assertEquals(5.42, $decimal->toNative());
        $decimal = new Decimal;
        $this->assertNull($decimal->toNative());
    }

    public function testIsEmpty()
    {
        $decimal = new Decimal(0.0);
        $this->assertFalse($decimal->isEmpty());
        $decimal = new Decimal;
        $this->assertTrue($decimal->isEmpty());
        $decimal = new Decimal(null);
        $this->assertTrue($decimal->isEmpty());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new Decimal(42);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueString()
    {
        new Decimal('foo');
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Decimal(true);
    }
}
