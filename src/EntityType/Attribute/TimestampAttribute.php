<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Timestamp;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;

final class TimestampAttribute implements AttributeInterface
{
    use AttributeTrait;

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
