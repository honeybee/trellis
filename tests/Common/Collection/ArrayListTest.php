<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Common\Collection\ArrayList;
use Trellis\Common\Collection\CollectionChangedEvent;
use Trellis\Common\Collection\CollectionInterface;
use Trellis\Common\Collection\ListInterface;
use Trellis\Common\Collection\ListenerInterface;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\TestCase;
use Mockery;

class ArrayListTest extends TestCase
{
    public function testCreate()
    {
        $items = $this->createRandomItems();
        $list = new ArrayList($items);

        $this->assertInstanceOf(CollectionInterface::CLASS, $list);
        $this->assertInstanceOf(ListInterface::CLASS, $list);
        $this->assertEquals(count($items), $list->getSize());
    }

    public function testPush()
    {
        $listener = Mockery::mock(ListenerInterface::CLASS);
        $listener->shouldReceive('onCollectionChanged')
            ->with(CollectionChangedEvent::CLASS)
            ->once();

        $items = $this->createRandomItems();

        $list = new ArrayList($items);
        $list->addListener($listener);

        $prev_count = $list->getSize();
        $new_item = TestObject::createRandomInstance();
        $list->push($new_item);

        $this->assertEquals($prev_count + 1, $list->getSize());
        $this->assertEquals($new_item, $list->getLast());
    }

    public function testPop()
    {
        $listener = Mockery::mock(ListenerInterface::CLASS);
        $listener->shouldReceive('onCollectionChanged')
            ->with(CollectionChangedEvent::CLASS)
            ->once();

        $items = $this->createRandomItems();

        $list = new ArrayList($items);
        $list->addListener($listener);

        $prev_count = $list->getSize();
        $last_item = $list->getLast();
        $popped_item = $list->pop();

        $this->assertEquals($prev_count - 1, $list->getSize());
        $this->assertEquals($last_item, $popped_item);
    }

    public function testShift()
    {
        $items = $this->createRandomItems();
        $list = new ArrayList($items);

        $first_item = $list->getFirst();
        $prev_count = $list->getSize();
        $shifted_item = $list->shift();

        $this->assertEquals($prev_count - 1, $list->getSize());
        $this->assertEquals($first_item, $shifted_item);
    }

    public function testUnshift()
    {
        $items = $this->createRandomItems();
        $list = new ArrayList($items);

        $prev_count = $list->getSize();
        $new_item = TestObject::createRandomInstance();
        $list->unshift($new_item);

        $this->assertEquals($prev_count + 1, $list->getSize());
        $this->assertEquals($new_item, $list->getFirst());
    }

    public function testAddItem()
    {
        $items = $this->createRandomItems();

        $list = new ArrayList();
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

    public function testAddItems()
    {
        $initial_items = $this->createRandomItems();
        $initial_items_count = count($initial_items);

        $items = $this->createRandomItems();
        $more_items_count = count($items);

        $list = new ArrayList($initial_items);
        $list->addItems($items);

        // assert item count
        $expected_item_count = $initial_items_count + $more_items_count;
        $this->assertEquals($expected_item_count, count($list));

        // assert item order
        foreach ($list as $index => $item) {
            $expected_item = null;
            if ($index < $initial_items_count) {
                $expected_item = $initial_items[$index];
            } else {
                $expected_item = $items[$index - $initial_items_count];
            }

            $this->assertEquals($expected_item, $item);
        }
    }

    public function testGetItem()
    {
        $items = $this->createRandomItems();
        $list = new ArrayList($items);
        $keys = array_keys($items);

        $item = $list->getItem(0);
        $this->assertEquals($items[0], $item);
    }

    public function testGetItems()
    {
        $items = $this->createRandomItems();
        $list = new ArrayList($items);
        $all_keys = array_keys($items);
        $item_count = count($items);
        $keys = [];
        for ($i = 0; $i < $item_count && $i <= 3; $i++) {
            $keys[] = $i;
        }

        $returned_items = $list->getItems($keys);
        $this->assertEquals(count($keys), count($returned_items));
        foreach ($keys as $key) {
            $item = $list->getItem($key);
            $this->assertEquals($item, $returned_items[$key]);
        }
    }

    public function testRemoveItem()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        $random_index = 0;

        // pick a random item from the list data to be removed
        if ($last_index > 0) {
            $random_index = self::$faker->numberBetween(0, $last_index);
        }
        $random_item = $items[$random_index];

        $list = new ArrayList($items);
        $list->removeItem($random_item);

        // assert item count
        $expected_item_count = $items_count - 1;
        $this->assertEquals($expected_item_count, count($list));

        // assert item order
        $expected_items = [];
        foreach ($items as $index => $item) {
            if ($index !== $random_index) {
                $expected_items[] = $item;
            }
        }

        foreach ($list as $index => $item) {
            $this->assertEquals($expected_items[$index], $item);
        }
    }

    public function testRemoveItems()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;

        // pick some random items to remove
        $max_remove = round($items_count / 2, 0);
        $numof_items_to_remove = self::$faker->numberBetween(1, $max_remove);
        $random_items = [];
        $randomly_picked_indexes = [];
        for ($i = 0; $i < $numof_items_to_remove; $i++) {
            $random_index = 0;
            do {
                if ($last_index > 0) {
                    $random_index = self::$faker->numberBetween(0, $last_index);
                }
            } while (in_array($random_index, $randomly_picked_indexes));

            $random_items[] = $items[$random_index];
            $randomly_picked_indexes[] = $random_index;
        }

        $list = new ArrayList($items);
        $list->removeItems($random_items);

        // assert item count
        $expected_item_count = $items_count - count($random_items);
        $this->assertEquals($expected_item_count, count($list));

        // assert item order
        $expected_items = [];
        foreach ($items as $index => $item) {
            if (!in_array($index, $randomly_picked_indexes)) {
                $expected_items[] = $item;
            }
        }

        foreach ($list as $index => $item) {
            $this->assertEquals($expected_items[$index], $item);
        }
    }

    public function testGetFirst()
    {
        $items = $this->createRandomItems();
        $first_item = $items[0];

        $list = new ArrayList($items);
        $this->assertEquals($first_item, $list->getFirst());
    }

    public function testGetLast()
    {
        $items = $this->createRandomItems();
        $last_item = $items[count($items) - 1];

        $list = new ArrayList($items);
        $this->assertEquals($last_item, $list->getLast());
    }

    public function testGetOffsetFirst()
    {
        $items = $this->createRandomItems();
        $first_item = $items[0];

        $list = new ArrayList($items);
        $this->assertEquals($first_item, $list[0]);
    }

    public function testGetOffsetLast()
    {
        $items = $this->createRandomItems();
        $last_item = $items[count($items) - 1];

        $list = new ArrayList($items);
        $this->assertEquals($last_item, $list[count($items) - 1]);
    }

    public function testGetSize()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);

        $list = new ArrayList($items);
        $this->assertEquals($items_count, $list->getSize());
    }

    public function testHasKey()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->numberBetween(0, $last_index);
        }

        $list = new ArrayList($items);
        $this->assertEquals(true, $list->hasKey($random_key));
        $this->assertEquals(false, $list->hasKey($items_count + 1));
    }

    public function testGetKey()
    {
        $items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->numberBetween(0, $last_index);
        }
        $random_item = $items[$random_key];

        $list = new ArrayList($items);
        $this->assertEquals($random_key, $list->getKey($random_item));
    }

    public function testHasItem()
    {
        $items = $this->createRandomItems();
        $other_items = $this->createRandomItems();
        $items_count = count($items);
        $last_index = $items_count - 1;
        // pick a random item from the list to test against
        $random_key = 0;
        if ($last_index > 0) {
            $random_key = self::$faker->numberBetween(0, $last_index);
        }
        $random_item = $items[$random_key];

        $list = new ArrayList($items);
        $this->assertEquals(true, $list->hasItem($random_item));
        $this->assertEquals(false, $list->hasItem($other_items[0]));
    }

    public function testEmptyListSize()
    {
        $empty_list = new ArrayList();

        $this->assertEquals(0, $empty_list->getSize());
    }

    public function testEmptyListGetFirst()
    {
        $empty_list = new ArrayList();

        $this->assertNull($empty_list->getFirst());
    }

    public function testEmptyListGetLast()
    {
        $empty_list = new ArrayList();

        $this->assertNull($empty_list->getLast());
    }

    public function testReverse()
    {
        $items = $this->createRandomItems();
        $item_count = count($items);
        $list = new ArrayList($items);
        $reversed_list = $list->reverse();

        foreach ($reversed_list as $i => $item) {
            $reverse_index = $item_count - $i - 1;
            $this->assertEquals($items[$reverse_index], $item);
        }
    }

    public function tearDown()
    {
        Mockery::close();
    }

    protected function createRandomItems()
    {
        return TestObject::createRandomInstances();
    }
}
