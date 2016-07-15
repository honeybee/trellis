<?php

namespace Trellis\EntityType\Attribute\Date;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class DateAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value = null, EntityInterface $parent = null)
    {
        if ($value instanceof Date) {
            return $value;
        }

        if (is_string($value)) {
            return Date::createFromString($value);
        }
        return $value !== null ? new Date($value) : new Date;
    }
}
