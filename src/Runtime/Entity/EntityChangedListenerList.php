<?php

namespace Trellis\Runtime\Entity;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueValueInterface;

/**
 * Represents a list of entity-changed listeners.
 */
class EntityChangedListenerList extends TypedList implements UniqueValueInterface
{
    /**
     * Returns the EntityChangedListenerInterface interface-name to the TypeList parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return EntityChangedListenerInterface::CLASS;
    }
}
