<?php

namespace Trellis\Tests\EntityType\Attribute\TextList;

use Trellis\EntityType\Attribute\TextList\TextList;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class TextListTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new TextList([ 'hello word!' ]));
    }

    public function testToNative()
    {
        $native_val = [ 'hello world!' ];
        $text_list = new TextList($native_val);
        $this->assertEquals($native_val, $text_list->toNative());
        $text_list = new TextList;
        $this->assertEquals([], $text_list->toNative());
    }

    public function testIsEmpty()
    {
        $text_list = new TextList([ 'hello world!' ]);
        $this->assertFalse($text_list->isEmpty());
        $text_list = new TextList;
        $this->assertTrue($text_list->isEmpty());
        $text_list = new TextList([]);
        $this->assertTrue($text_list->isEmpty());
    }

    /**
     * @expectedException \Trellis\Exception
     */
    public function testInvalidMixedArray()
    {
        new TextList([ 42.0, 'foo', 23 ]);
    } // @codeCoverageIgnore
}
