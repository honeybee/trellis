<?php

namespace Trellis\Runtime;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueValueInterface;
use Trellis\Runtime\EntityTypeInterface;

class EntityTypeMap extends TypedMap implements UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return EntityTypeInterface::CLASS;
    }
}
