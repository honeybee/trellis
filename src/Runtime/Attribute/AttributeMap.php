<?php

namespace Trellis\Runtime\Attribute;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueValueInterface;
use Trellis\Runtime\Attribute\AttributeInterface;
use Twig\Error\RuntimeError;

/**
 * AttributeMap is a associative collection container, that maps attribute names to correspondig attribute instances.
 * As attributes must be unique by name, it is not recommended using this class outside of a type's scope.
 */
class AttributeMap extends TypedMap implements UniqueValueInterface
{
    /**
     * Initializes the map with the provided attribute names as keys
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attrs = [];
        foreach ($attributes as $attribute) {
            if (!$attribute instanceof AttributeInterface) {
                throw new RuntimeError('Given attributes must implement '.AttributeInterface::class);
            }
            $attrs[$attribute->getName()] = $attribute;
        }
        parent::__construct($attrs);
    }

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
