<?php

namespace Trellis\Tests\Attribute\Text;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\Text\Text;
use Trellis\Tests\TestCase;
use Trellis\Value\ValueInterface;

class TextTest extends TestCase
{
    public function testConstruct()
    {
        $attribute = $this->getMockBuilder(AttributeInterface::class)->getMock();
        $this->assertInstanceOf(ValueInterface::CLASS, new Text($attribute, 'foobar'));
    }

    public function testToNative()
    {
        $attribute = $this->getMockBuilder(AttributeInterface::class)->getMock();
        $text = new Text($attribute, 'foobar');

        $this->assertEquals('foobar', $text->toNative());
    }
}
