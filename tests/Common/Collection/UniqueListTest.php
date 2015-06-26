<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\TestCase;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\Common\Collection\Fixtures\UniqueTestObjectList;

use Faker;

class UniqueListTest extends TestCase
{
    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testUniqueness()
    {
        $items = TestObject::createRandomInstances();

        $list = new UniqueTestObjectList($items);
        $list->addItem($items[0]);
    }
}
