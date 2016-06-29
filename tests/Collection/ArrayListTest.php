<?php

namespace Trellis\Tests\Collection;

use Trellis\Collection\ArrayList;
use Trellis\Collection\CollectionInterface;
use Trellis\Collection\ListInterface;
use Trellis\Tests\TestCase;

class ArrayListTest extends TestCase
{
    public function testConstruct()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);

        $this->assertInstanceOf(CollectionInterface::CLASS, $list);
        $this->assertInstanceOf(ListInterface::CLASS, $list);
    }

    public function testPush()
    {
        $list = new ArrayList([ 'foo', 'bar' ]);
        $new_list = $list->push('foobar');

        $this->assertEquals($list->getSize() + 1, $new_list->getSize());
        $this->assertEquals('foobar', $new_list->getLast());
    }

    public function testPop()
    {
        $list = new ArrayList([ 'foo', 'bar' ]);
        $new_list = $list->pop();

        $this->assertEquals($list->getSize() - 1, $new_list->getSize());
        $this->assertFalse($new_list->getKey('bar'));
    }

    public function testShift()
    {
        $list = new ArrayList([ 'foo', 'bar' ]);
        $new_list = $list->shift();

        $this->assertEquals($list->getSize() - 1, $new_list->getSize());
        $this->assertFalse($new_list->getKey('foo'));
    }

    public function testUnshift()
    {
        $list = new ArrayList([ 'foo', 'bar' ]);
        $new_list = $list->unshift('barfoo');

        $this->assertEquals($list->getSize() + 1, $new_list->getSize());
        $this->assertEquals('barfoo', $new_list->getFirst());
    }

    public function testGetValue()
    {
        $list = new ArrayList([ 'foo', 'bar' ]);

        $this->assertEquals('foo', $list->getValue(0));
        $this->assertEquals('bar', $list->getValue(1));
    }

    public function testGetValues()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);

        $this->assertEquals([ 1 => 'bar', 2 => 'foobar' ], $list->getValues([ 1, 2 ]));
    }

    public function testWithoutValue()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);
        $new_list = $list->withoutValue('bar');

        $this->assertEquals($list->getSize() - 1, $new_list->getSize());
        $this->assertFalse($new_list->getKey('bar'));
    }

    public function testWithoutValues()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);
        $new_list = $list->withoutValues([ 'foo', 'foobar' ]);

        $this->assertEquals($list->getSize() - 2, $new_list->getSize());
        $this->assertFalse($new_list->getKey('foo'));
        $this->assertFalse($new_list->getKey('foobar'));
    }

    public function testGetFirst()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);
        $this->assertEquals('foo', $list->getFirst());
    }

    public function testGetLast()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);
        $this->assertEquals('foobar', $list->getLast());
    }

    public function testGetOffsetFirst()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);
        $this->assertEquals('foo', $list[0]);
    }

    public function testGetOffsetLast()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);
        $this->assertEquals('foobar', $list[2]);
    }

    public function testHasKey()
    {
        $list = new ArrayList([ 'foo', 'bar', 'foobar' ]);
        $this->assertTrue($list->hasKey(2));
        $this->assertFalse($list->hasKey(3));
    }

    public function testHasValue()
    {
        $list = new ArrayList([ 'foo', 'bar' ]);
        $this->assertTrue($list->hasValue('foo'));
        $this->assertFalse($list->hasValue('foobar'));
    }

    public function testEmptyListSize()
    {
        $empty_list = new ArrayList();

        $this->assertEquals(0, $empty_list->getSize());
    }
}
