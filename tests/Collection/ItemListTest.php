<?php

namespace Trellis\Tests\Collection;

use Trellis\Collection\ItemList;
use Trellis\Collection\CollectionInterface;
use Trellis\Collection\ListInterface;
use Trellis\Tests\TestCase;

class ItemListTest extends TestCase
{
    public function testConstruct()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);

        $this->assertInstanceOf(CollectionInterface::CLASS, $list);
        $this->assertInstanceOf(ListInterface::CLASS, $list);
    }

    public function testPush()
    {
        $list = new ItemList([ 'foo', 'bar' ]);
        $new_list = $list->push('foobar');

        $this->assertEquals($list->getSize() + 1, $new_list->getSize());
        $this->assertEquals('foobar', $new_list->getLast());
    }

    public function testPop()
    {
        $list = new ItemList([ 'foo', 'bar' ]);
        $new_list = $list->pop();

        $this->assertEquals($list->getSize() - 1, $new_list->getSize());
        $this->assertFalse($new_list->getKey('bar'));
    }

    public function testShift()
    {
        $list = new ItemList([ 'foo', 'bar' ]);
        $new_list = $list->shift();

        $this->assertEquals($list->getSize() - 1, $new_list->getSize());
        $this->assertFalse($new_list->getKey('foo'));
    }

    public function testUnshift()
    {
        $list = new ItemList([ 'foo', 'bar' ]);
        $new_list = $list->unshift('barfoo');

        $this->assertEquals($list->getSize() + 1, $new_list->getSize());
        $this->assertEquals('barfoo', $new_list->getFirst());
    }

    public function testGetItem()
    {
        $list = new ItemList([ 'foo', 'bar' ]);

        $this->assertEquals('foo', $list->getItem(0));
        $this->assertEquals('bar', $list->getItem(1));
    }

    public function testGetItems()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);

        $this->assertEquals([ 1 => 'bar', 2 => 'foobar' ], $list->getItems([ 1, 2 ]));
    }

    public function testWithoutItem()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);
        $new_list = $list->withoutItem('bar');

        $this->assertEquals($list->getSize() - 1, $new_list->getSize());
        $this->assertFalse($new_list->getKey('bar'));
    }

    public function testWithoutItems()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);
        $new_list = $list->withoutItems([ 'foo', 'foobar' ]);

        $this->assertEquals($list->getSize() - 2, $new_list->getSize());
        $this->assertFalse($new_list->getKey('foo'));
        $this->assertFalse($new_list->getKey('foobar'));
    }

    public function testGetFirst()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);
        $this->assertEquals('foo', $list->getFirst());
    }

    public function testGetLast()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);
        $this->assertEquals('foobar', $list->getLast());
    }

    public function testGetOffsetFirst()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);
        $this->assertEquals('foo', $list[0]);
    }

    public function testGetOffsetLast()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);
        $this->assertEquals('foobar', $list[2]);
    }

    public function testHasKey()
    {
        $list = new ItemList([ 'foo', 'bar', 'foobar' ]);
        $this->assertTrue($list->hasKey(2));
        $this->assertFalse($list->hasKey(3));
    }

    public function testHasItem()
    {
        $list = new ItemList([ 'foo', 'bar' ]);
        $this->assertTrue($list->hasItem('foo'));
        $this->assertFalse($list->hasItem('foobar'));
    }

    public function testEmptyListSize()
    {
        $empty_list = new ItemList();

        $this->assertEquals(0, $empty_list->getSize());
    }

    public function testAppend()
    {
        $list = new ItemList([ 'foo', 'bar' ]);
        $new_list = $list->append(new ItemList([ 'hello', 'world' ]));

        $this->assertEquals(4, $new_list->getSize());
        $this->assertEquals('hello', $new_list[2]);
        $this->assertEquals('world', $new_list[3]);
    }
}
