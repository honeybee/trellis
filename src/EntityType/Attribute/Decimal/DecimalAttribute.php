<?php

namespace Trellis\EntityType\Attribute\Decimal;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class DecimalAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value = null, EntityInterface $parent = null)
    {
        if ($value instanceof Decimal) {
            return $value;
        }
        return $value !== null ? new Decimal($value) : new Decimal;
    }
}
