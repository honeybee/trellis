<?php

namespace Trellis\Runtime\ValueHolder;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueValueInterface;

class ValueChangedListenerList extends TypedList implements UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return ValueChangedListenerInterface::CLASS;
    }
}
