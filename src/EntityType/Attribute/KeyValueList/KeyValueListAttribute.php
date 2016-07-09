<?php

namespace Trellis\EntityType\Attribute\KeyValueList;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class KeyValueListAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof KeyValueList) {
            return $value;
        }
        return $value !== null ? new KeyValueList($value) : new KeyValueList;
    }
}
