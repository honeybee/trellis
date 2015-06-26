<?php

namespace Trellis\Tests\Common\Collection\Fixtures;

use Trellis\Common\Collection\TypedList;

class TestObjectList extends TypedList
{
    protected function getItemImplementor()
    {
        return '\\Trellis\\Tests\\Common\\Fixtures\\TestObject';
    }
}
