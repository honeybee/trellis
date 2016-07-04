<?php

namespace Trellis\Tests\Collection;

use Trellis\Collection\CollectionInterface;
use Trellis\Collection\ItemList;
use Trellis\Collection\Map;
use Trellis\Collection\MapInterface;
use Trellis\Tests\TestCase;

class MapTest extends TestCase
{
    public function testConstruct()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);

        $this->assertInstanceOf(CollectionInterface::CLASS, $map);
        $this->assertInstanceOf(MapInterface::CLASS, $map);
    }

    public function testGeyKeys()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);

        $this->assertEquals([ 'foo', 'msg'], $map->getKeys());
    }

    public function testFilter()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);
        $new_map = $map->filter(function ($value) {
            return $value === 'bar';
        });

        $this->assertCount(1, $new_map);
        $this->assertEquals([ 'foo' => 'bar'], $new_map->toArray());
    }

    public function testFilterWithoutChange()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);
        $new_map = $map->filter(function ($value) {
            return true;
        });

        $this->assertEquals($map, $new_map);
    }

    public function testWithItems()
    {
        $map = new Map([ 'foo' => 'bar' ]);
        $new_map = $map->withItems([ 'msg' => 'hello world!', 'foobar' => 'barfoo' ]);

        $this->assertEquals(
            [ 'foo' => 'bar', 'msg' => 'hello world!', 'foobar' => 'barfoo' ],
            $new_map->toArray()
        );
    }

    public function testWithExistingItem()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);

        $this->assertEquals($map, $map->withItem('foo', 'bar'));
    }

    public function testWithoutNonExistingItem()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);

        $this->assertEquals($map, $map->withoutItem('foobar'));
    }

    public function testWithoutNonExistingItems()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);

        $this->assertEquals($map, $map->withoutItems([ 'foobar', 'barfoo' ]));
    }

    public function testWithDifferentItem()
    {
        $map = new Map([ 'foo' => 'bar' ]);
        $new_map = $map->withItem('msg', 'hello world!');

        $this->assertNotEquals($map, $new_map);
        $this->assertEquals([ 'foo' => 'bar', 'msg' => 'hello world!' ], $new_map->toArray());
    }

    /**
     * @expectedException \Trellis\Exception
     */
    public function testInvalidAppendItem()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);
        $map->append(new ItemList([ 'foo', 'bar' ]));
    } // @codeCoverageIgnore
}
