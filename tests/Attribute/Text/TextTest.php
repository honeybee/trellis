<?php

namespace Trellis\Tests\Attribute\Text;

use Trellis\Attribute\Text\Text;
use Trellis\Tests\TestCase;
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
