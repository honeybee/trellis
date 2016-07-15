<?php

namespace Trellis\EntityType\Attribute\Timestamp;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class TimestampAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value = null, EntityInterface $parent = null)
    {
        if ($value instanceof Timestamp) {
            return $value;
        }

        if (is_string($value)) {
            return Timestamp::createFromString($value);
        }
        return $value !== null ? new Timestamp($value) : new Timestamp;
    }
}
