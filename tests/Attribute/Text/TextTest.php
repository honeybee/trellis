<?php

namespace Trellis\Tests\Attribute\Text;

use Trellis\Attribute\Text\Text;
use Trellis\Tests\TestCase;
use Trellis\Value\ValueInterface;

class TextTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Text('hello word!'));
    }

    public function testToNative()
    {
        $text = new Text('hello world!');
        $this->assertEquals('hello world!', $text->toNative());
        $text = new Text;
        $this->assertEquals('', $text->toNative());
    }

    public function testIsEmpty()
    {
        $text = new Text('hello world!');
        $this->assertFalse($text->isEmpty());
        $text = new Text;
        $this->assertTrue($text->isEmpty());
        $text = new Text('');
        $this->assertTrue($text->isEmpty());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueFloat()
    {
        new Text(42.0);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new Text(23);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Text(true);
    }
}
