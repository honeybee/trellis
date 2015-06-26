<?php

namespace Trellis\Runtime\ValueHolder;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueCollectionInterface;

class ValueHolderMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return ValueHolderInterface::CLASS;
    }
}
