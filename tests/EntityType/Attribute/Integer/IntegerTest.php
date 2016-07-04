<?php

namespace Trellis\Tests\EntityType\Attribute\Integer;

use Trellis\EntityType\Attribute\Integer\Integer;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

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
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueString()
    {
        new Integer('foo');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Integer(true);
    } // @codeCoverageIgnore
}
