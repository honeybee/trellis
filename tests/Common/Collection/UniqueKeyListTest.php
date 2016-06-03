<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\Common\Collection\Fixtures\UniqueKeyTestObjectList;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\TestCase;

class UniqueKeyListTest extends TestCase
{
    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testKeyUniqueness()
    {
        $items = TestObject::createRandomInstances();

        $list = new UniqueKeyTestObjectList($items);
        $list[0] = new TestObject;
    }

    public function testKeyUnset()
    {
        $items = TestObject::createRandomInstances();

        $list = new UniqueKeyTestObjectList($items);
        $count_before = $list->getSize();
        unset($list[0]);

        $this->assertEquals($count_before - 1, $list->getSize());
    }
}
