<?php

namespace Trellis\Runtime\ValueHolder;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueCollectionInterface;

class ValueChangedListenerList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return ValueChangedListenerInterface::CLASS;
    }
}
