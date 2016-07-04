<?php

namespace Trellis\Tests\Attribute\Integer;

use Trellis\Attribute\Integer\Integer;
use Trellis\Tests\TestCase;
use Trellis\Value\ValueInterface;

class IntegerTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Integer(42));
    }

    public function testToNative()
    {
        $integer = new Integer(5);
        $this->assertEquals(5, $integer->toNative());
        $integer = new Integer;
        $this->assertNull($integer->toNative());
    }

    public function testIsEmpty()
    {
        $integer = new Integer(42);
        $this->assertFalse($integer->isEmpty());
        $integer = new Integer;
        $this->assertTrue($integer->isEmpty());
        $integer = new Integer(null);
        $this->assertTrue($integer->isEmpty());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueFloat()
    {
        new Integer(42.0);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueString()
    {
        new Integer('foo');
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Integer(true);
    }
}
