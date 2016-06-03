<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\Common\Collection\Fixtures\UniqueKeyTestObjectMap;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\TestCase;

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

    public function testKeyUnset()
    {
        $items = $this->createRandomItems();

        $map = new UniqueKeyTestObjectMap($items);
        $keys = $map->getKeys();

        $this->assertTrue($map->hasKey($keys[0]));

        unset($map[$keys[0]]);

        $this->assertFalse($map->hasKey($keys[0]));
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
