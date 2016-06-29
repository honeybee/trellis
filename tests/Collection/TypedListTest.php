<?php

namespace Trellis\Tests\Collection;

use Trellis\Collection\CollectionInterface;
use Trellis\Collection\ListInterface;
use Trellis\Collection\TypedList;
use Trellis\Tests\TestCase;

class TypedListTest extends TestCase
{
    public function testConstruct()
    {
        $list = new TypedList('string', [ 'bar', 'hello world!' ]);

        $this->assertInstanceOf(CollectionInterface::CLASS, $list);
        $this->assertInstanceOf(ListInterface::CLASS, $list);
    }

    /**
     * @expectedException Trellis\Exception
     */
    public function testConstructWithInvalidValues()
    {
        $list = new TypedList('string', [ 23, 42 ]);

        $this->assertInstanceOf(CollectionInterface::CLASS, $list);
        $this->assertInstanceOf(ListInterface::CLASS, $list);
    }
}
