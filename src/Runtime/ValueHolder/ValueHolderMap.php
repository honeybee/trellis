<?php

namespace Trellis\Runtime\ValueHolder;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueValueInterface;

class ValueHolderMap extends TypedMap implements UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return ValueHolderInterface::CLASS;
    }
}
