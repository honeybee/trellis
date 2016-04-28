<?php

namespace Trellis\Runtime\ValueHolder;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueValueInterface;

/**
 * Represents a list of value-changed events.
 */
class ValueChangedEventList extends TypedList implements UniqueValueInterface
{
    /**
     * Returns the ValueChangedEvent class-name to the TypeList parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return ValueChangedEvent::CLASS;
    }
}
