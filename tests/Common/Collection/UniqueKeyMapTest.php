<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\TestCase;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\Common\Collection\Fixtures\UniqueKeyTestObjectMap;

use Faker;

class UniqueKeyMapTest extends TestCase
{
    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testKeyUniqueness()
    {
        $items = $this->createRandomItems();

        $map = new UniqueKeyTestObjectMap($items);
        $keys = $map->getKeys();
        $map->setItem($keys[0], new TestObject);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testKeyUnsettable()
    {
        $items = $this->createRandomItems();

        $map = new UniqueKeyTestObjectMap($items);
        $keys = $map->getKeys();
        unset($map[$keys[0]]);
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
