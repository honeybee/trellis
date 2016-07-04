<?php

namespace Trellis\Attribute\Timestamp;

use Trellis\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class TimestampAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if (is_string($value)) {
            return Timestamp::createFromString($value);
        }
        return $value !== null ? new Timestamp($value) : new Timestamp;
    }
}
