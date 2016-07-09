<?php

namespace Trellis\Tests\EntityType\Attribute\KeyValueList;

use Trellis\EntityType\Attribute\KeyValueList\KeyValueList;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class KeyValueListTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new KeyValueList([ 'message' => 'hello world!' ]));
    }

    public function testToNative()
    {
        $key_value_list = new KeyValueList([ 'message' => 'hello world!' ]);
        $this->assertEquals([ 'message' => 'hello world!' ], $key_value_list->toNative());
        $key_value_list = new KeyValueList;
        $this->assertEquals([], $key_value_list->toNative());
    }

    public function testIsEmpty()
    {
        $key_value_list = new KeyValueList([ 'message' => 'hello world!' ]);
        $this->assertFalse($key_value_list->isEmpty());
        $key_value_list = new KeyValueList;
        $this->assertTrue($key_value_list->isEmpty());
        $key_value_list = new KeyValueList([]);
        $this->assertTrue($key_value_list->isEmpty());
    }
}
