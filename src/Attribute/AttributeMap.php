<?php

namespace Trellis\Attribute;

use Trellis\Collection\TypedMap;

class AttributeMap extends TypedMap
{
    /**
     * @param AttributeInterface $attributes
     */
    public function __construct(array $attributes = [])
    {
        $mapped_attributes = [];
        foreach ($attributes as $attribute) {
            $mapped_attributes[$attribute->getName()] = $attribute;
        }

        parent::__construct(AttributeInterface::CLASS, $mapped_attributes);
    }
}
