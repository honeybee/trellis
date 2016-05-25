<?php

namespace Trellis\Tests\Common\Collection;

use Mockery;
use Trellis\Tests\TestCase;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Common\Collection\Map;

use Faker;

class MapTest extends TestCase
{
    public function testCreate()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);

        $this->assertInstanceOf('\\Trellis\\Common\\Collection\\CollectionInterface', $map);
        $this->assertInstanceOf('\\Trellis\\Common\\Collection\\MapInterface', $map);
    }

    public function testGetItem()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);
        $keys = array_keys($items);

        $item = $map->getItem($keys[0]);
        $this->assertEquals($items[$keys[0]], $item);
    }

    public function testGetItems()
    {
        $expected_items = $this->createRandomItems();
        $map = new Map($expected_items);
        $all_keys = array_keys($expected_items);
        $item_count = count($expected_items);

        $keys = [];
        for ($i = 0; $i < $item_count && $i <= 3; $i++) {
            $keys[] = $all_keys[$i];
        }

        $actual_items = $map->getItems($keys);
        $this->assertEquals(count($keys), count($actual_items));

        foreach ($keys as $idx => $key) {
            $item = $map->getItem($key);
            $this->assertEquals($item, $actual_items[$idx]);
        }
    }

    public function testSetItem()
    {
        // test that we are receiving the correct number of expected collection changed events.
        // in this case we are expecting only one for the single item we are adding.
        $listener = Mockery::mock('\Trellis\Common\Collection\ListenerInterface');
        $listener->shouldReceive('onCollectionChanged')->with(
            '\Trellis\Common\Collection\CollectionChangedEvent'
        )->once();

        $map = new Map($this->createRandomItems());
        $map->addListener($listener);

        $start_size = $map->getSize();
        $key = 'Faker-does-not-have-this-charsequence-in-its-word-list-for-lorem-ipsum!';
        $item = TestObject::createRandomInstances();
        $map->setItem($key, $item);

        $this->assertEquals($item, $map->getItem($key));
        $this->assertEquals($start_size + 1, $map->getSize());
    }

    public function testSetItems()
    {
        $start_items = $this->createRandomItems();
        $map = new Map($start_items);
        $start_size = $map->getSize();

        $new_items = $this->createRandomItems();
        $new_keys = array_diff(array_keys($new_items), array_keys($start_items));

        // test that we are receiving the correct number of expected collection changed events.
        // in this case we are expecting one event for each new key/item.
        $listener = Mockery::mock('\Trellis\Common\Collection\ListenerInterface');
        $listener->shouldReceive('onCollectionChanged')->with(
            '\Trellis\Common\Collection\CollectionChangedEvent'
        )->times(count($new_items));

        $map->addListener($listener);
        $map->setItems($new_items);

        $this->assertEquals($start_size + count($new_keys), $map->getSize());
    }

    public function testRemoveItem()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);
        $start_size = $map->getSize();

        $keys = array_keys($items);
        $first_key = $keys[0];
        $map->removeItem($items[$first_key]);

        $this->assertEquals($start_size - 1, $map->getSize());
    }

    public function testRemoveNonExistentItem()
    {
        $items = $this->createRandomItems();
        $shifted_item = array_shift($items);
        $map = new Map($items);
        $start_size = $map->getSize();

        $map->removeItem($shifted_item);

        $this->assertEquals($start_size, $map->getSize());
    }

    public function testRemoveItems()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);

        $map->removeItems(array_values($items));

        $this->assertEquals(0, $map->getSize());
    }

    public function testGetKeys()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);

        $this->assertEquals(array_keys($items), $map->getKeys());
    }

    public function testGetValues()
    {
        $items = $this->createRandomItems();
        $map = new Map($items);

        $this->assertEquals(array_values($items), $map->getValues());
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
