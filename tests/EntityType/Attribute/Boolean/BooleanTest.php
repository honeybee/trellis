<?php

namespace Trellis\Tests\EntityType\Attribute\Boolean;

use Trellis\EntityType\Attribute\Boolean\Boolean;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class BooleanTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Boolean(true));
    }

    public function testToNative()
    {
        $boolean = new Boolean(true);
        $this->assertTrue($boolean->toNative());
        $boolean = new Boolean(false);
        $this->assertFalse($boolean->toNative());
        $boolean = new Boolean;
        $this->assertFalse($boolean->toNative());
    }

    public function testIsEmpty()
    {
        $boolean = new Boolean(true);
        $this->assertFalse($boolean->isEmpty());
        $boolean = new Boolean(false);
        $this->assertTrue($boolean->isEmpty());
        $boolean = new Boolean;
        $this->assertTrue($boolean->isEmpty());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueFloat()
    {
        new Boolean(42.0);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueString()
    {
        new Boolean('foo');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new Boolean(23);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueNull()
    {
        new Boolean(null);
    } // @codeCoverageIgnore
}
