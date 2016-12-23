<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Date;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;

final class DateAttribute implements AttributeInterface
{
    use AttributeTrait;

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof Date) {
            return $value;
        }
        return $value !== null ? new Date($value) : new Date;
    }
}
