<?php

namespace Trellis\Tests\Value;

use Trellis\Tests\TestCase;
use Trellis\Value\Text;
use Trellis\Value\ValueInterface;

class TextTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Text('foobar'));
    }

    public function testToNative()
    {
        $any_value = new Text('foobar');

        $this->assertEquals('foobar', $any_value->toNative());
    }
}
