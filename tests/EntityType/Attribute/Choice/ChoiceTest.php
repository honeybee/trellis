<?php

namespace Trellis\Tests\EntityType\Attribute\Choice;

use Trellis\EntityType\Attribute\Choice\Choice;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class ChoiceTest extends TestCase
{
    protected static $allowed_values = [ 'foo', 'bar', 'foobar' ];

    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Choice(self::$allowed_values));
    }

    public function testToNative()
    {
        $choice = new Choice(self::$allowed_values, 'foo');
        $this->assertEquals('foo', $choice->toNative());
        $choice = new Choice(self::$allowed_values);
        $this->assertEmpty($choice->toNative());
    }

    public function testIsEmpty()
    {
        $choice = new Choice(self::$allowed_values, 'foo');
        $this->assertFalse($choice->isEmpty());
        $choice = new Choice(self::$allowed_values);
        $this->assertTrue($choice->isEmpty());
        $choice = new Choice(self::$allowed_values, '');
        $this->assertTrue($choice->isEmpty());
    }

    public function testGetAllowedChoices()
    {
        $choice = new Choice(self::$allowed_values);
        $this->assertEquals(self::$allowed_values, $choice->getAllowedChoices());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidChoice()
    {
        new Choice(self::$allowed_values, 'hello world');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new Choice(self::$allowed_values, 42);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Choice(self::$allowed_values, true);
    } // @codeCoverageIgnore
}
