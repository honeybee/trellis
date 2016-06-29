<?php

namespace Trellis\Attribute;

use Equip\Structure\Dictionary;
use Trellis\Exception;

class AttributeMap extends Dictionary implements AttributeMapInterface
{
    public function withAttributesAdded(AttributeMapInterface $attributes)
    {
        $attribute_map = $this;
        foreach ($attributes as $attribute) {
            $attribute_map = $attribute_map->withValue($attribute->getName(), $attribute);
        }

        return $attribute_map;
    }

    protected function assertValid(array $values)
    {
        foreach ($values as $value) {
            if (!$value instanceof AttributeInterface) {
                throw new Exception('Only attribute instances are allowed to be passed to the ' . __CLASS__);
            }
        }
    }
}
