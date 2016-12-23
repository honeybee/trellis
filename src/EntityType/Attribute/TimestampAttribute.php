<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Timestamp;
use Trellis\EntityType\Attribute;

final class TimestampAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof Timestamp) {
            return $value;
        }
        return $value !== null ? new Timestamp($value) : new Timestamp;
    }
}
