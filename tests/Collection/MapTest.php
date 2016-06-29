<?php

namespace Trellis\Tests\Collection;

use Trellis\Collection\CollectionInterface;
use Trellis\Collection\Map;
use Trellis\Collection\MapInterface;
use Trellis\Tests\TestCase;

class MapTest extends TestCase
{
    public function testConstruct()
    {
        $map = new Map([ 'foo' => 'bar', 'msg' => 'hello world!' ]);

        $this->assertInstanceOf(CollectionInterface::CLASS, $map);
        $this->assertInstanceOf(MapInterface::CLASS, $map);
    }
}
