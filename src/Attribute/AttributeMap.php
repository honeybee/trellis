<?php

namespace Trellis\Attribute;

use Equip\Structure\Dictionary;
use Trellis\Collection\Map;
use Trellis\Exception;

class AttributeMap extends Map
{
    protected function assertValid(array $values)
    {
        foreach ($values as $value) {
            if (!$value instanceof AttributeInterface) {
                throw new Exception('Only attribute instances are allowed to be passed to the ' . __CLASS__);
            }
        }
    }
}
