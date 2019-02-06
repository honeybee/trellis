<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\TestCase;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\Common\Collection\Fixtures\TestObjectMap;
use Trellis\Tests\Common\Collection\Fixtures\UnsupportedObject;

use Faker;

class TypedMapTest extends TestCase
{
    public function testCreate()
    {
        $items = $this->createRandomItems();
        $map = new TestObjectMap($items);

        $this->assertInstanceOf('\\Trellis\\Common\\Collection\\CollectionInterface', $map);
        $this->assertInstanceOf('\\Trellis\\Common\\Collection\\MapInterface', $map);
        $this->assertInstanceOf('\\Trellis\\Common\\Collection\\TypedMap', $map);
        $this->assertEquals(count($items), $map->getSize());
    }

    public function testHasKeySucceeds()
    {
        $map = new TestObjectMap();
        $map->setItem('asdf', new TestObject());
        $this->assertTrue($map->hasKey('asdf'));
    }

    public function testHasItemSucceeds()
    {
        $map = new TestObjectMap();
        $foo = new TestObject();
        $map->setItem('asdf', $foo);
        $this->assertTrue($map->hasItem($foo));
    }

    public function testSetItem()
    {
        $items = $this->createRandomItems();

        $map = new TestObjectMap();
        foreach ($items as $key => $item) {
            $map->setItem($key, $item);
        }

        // assert item count
        $expected_item_count = count($items);
        $this->assertEquals($expected_item_count, count($map));

        // assert item keys
        foreach ($map as $key => $item) {
            $expected_item = $items[$key];
            $this->assertEquals($expected_item, $item);
        }
    }

    /**
     * @expectedException Trellis\Common\Error\InvalidTypeException
     */
    public function testSetInvalidItem()
    {
        $map = new TestObjectMap();
        $map->setItem("foobar", new UnsupportedObject());
    }

    protected function createRandomItems()
    {
        $items = [];
        $faker = Faker\Factory::create();
        foreach (TestObject::createRandomInstances() as $item) {
            $items[$faker->word(12)] = $item;
        }

        return $items;
    }
}
