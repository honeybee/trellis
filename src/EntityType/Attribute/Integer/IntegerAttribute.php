<?php

namespace Trellis\EntityType\Attribute\Integer;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class IntegerAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value = null, EntityInterface $parent = null)
    {
        if ($value instanceof Integer) {
            return $value;
        }
        return $value !== null ? new Integer($value) : new Integer;
    }
}
