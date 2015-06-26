<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\TestCase;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\Common\Collection\Fixtures\TestObjectList;
use Trellis\Tests\Common\Collection\Fixtures\UnsupportedObject;

use Faker;

class TypedListTest extends TestCase
{
    public function testCreate()
    {
        $items = TestObject::createRandomInstances();
        $list = new TestObjectList($items);

        $this->assertInstanceOf('\\Trellis\\Common\\Collection\\CollectionInterface', $list);
        $this->assertInstanceOf('\\Trellis\\Common\\Collection\\ListInterface', $list);
        $this->assertInstanceOf('\\Trellis\\Common\\Collection\\TypedList', $list);
        $this->assertEquals(count($items), $list->getSize());
    }

    public function testAddItem()
    {
        $items = TestObject::createRandomInstances();

        $list = new TestObjectList();
        foreach ($items as $item) {
            $list->addItem($item);
        }

        // assert item count
        $expected_item_count = count($items);
        $this->assertEquals($expected_item_count, count($list));

        // assert item order
        foreach ($list as $index => $object) {
            $expected_item = $items[$index];
            $this->assertEquals($expected_item, $object);
        }
    }

    /**
     * @expectedException Trellis\Common\Error\InvalidTypeException
     */
    public function testAddInvalidScalar()
    {
        $list = new TestObjectList();
        $list->addItem("foobar");
    }

    /**
     * @expectedException Trellis\Common\Error\InvalidTypeException
     */
    public function testAddInvalidObject()
    {
        $list = new TestObjectList();
        $list->addItem(new UnsupportedObject());
    }
}
