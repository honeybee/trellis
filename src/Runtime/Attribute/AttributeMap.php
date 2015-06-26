<?php

namespace Trellis\Runtime\Attribute;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueCollectionInterface;

/**
 * AttributeMap is a associative collection container, that maps attribute names to correspondig attribute instances.
 * As attributes must be unique by name, it is not recommended using this class outside of a type's scope.
 */
class AttributeMap extends TypedMap implements UniqueCollectionInterface
{
    /**
     * Returns the AttributeInterface interface-name to the TypeMap parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return AttributeInterface::CLASS;
    }
}
