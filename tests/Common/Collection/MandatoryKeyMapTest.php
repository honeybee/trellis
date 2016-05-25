<?php

namespace Trellis\Tests\Common\Collection;

use Trellis\Tests\TestCase;
use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\Common\Collection\Fixtures\MandatoryKeyTestObjectMap;

class MandatoryKeyMapTest extends TestCase
{
    public function testMandatoryKey()
    {
        $item = new TestObject;

        $map = new MandatoryKeyTestObjectMap([ 'foobar' => $item ]);
        $item = $map['foobar'];
        $this->assertInstanceOf(TestObject::CLASS, $item);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testMandatoryKeyNotFound()
    {
        $item = new TestObject;

        $map = new MandatoryKeyTestObjectMap([ 'foobar' => $item ]);
        $map->getItem('nonexistent');
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testUnsetMandatoryItem()
    {
        $map = new MandatoryKeyTestObjectMap;
        unset($map['nonexistentkey']);
    }
}
