<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\TestCase;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\Common\Collection\Fixtures\UniqueTestObjectMap;

use Faker;

class UniqueMapTest extends TestCase
{
    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testUniqueness()
    {
        $items = $this->createRandomItems();

        $map = new UniqueTestObjectMap($items);
        $keys = $map->getKeys();
        $map->setItem('foobar', $items[$keys[0]]);
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
