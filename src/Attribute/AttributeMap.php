<?php

namespace Trellis\Attribute;

use Trellis\Collection\TypedMap;

class AttributeMap extends TypedMap
{
    public function __construct(array $attributes = [])
    {
        parent::__construct(AttributeInterface::CLASS, $attributes);
    }
}
