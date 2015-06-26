<?php

namespace Trellis\Tests\Common\Collection\Fixtures;

use Trellis\Common\Collection\TypedMap;

class TestObjectMap extends TypedMap
{
    protected function getItemImplementor()
    {
        return '\\Trellis\\Tests\\Common\\Fixtures\\TestObject';
    }
}
