<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\TestCase;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\Common\Collection\Fixtures\UniqueKeyTestObjectList;

use Faker;

class UniqueKeyListTest extends TestCase
{
    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testKeyUniqueness()
    {
        $items = TestObject::createRandomInstances();

        $map = new UniqueKeyTestObjectList($items);
        $map[0] = new TestObject;
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testKeyUnsettable()
    {
        $items = TestObject::createRandomInstances();

        $map = new UniqueKeyTestObjectList($items);
        unset($map[0]);
    }
}
