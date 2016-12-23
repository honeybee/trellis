<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Decimal;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;

final class DecimalAttribute implements AttributeInterface
{
    use AttributeTrait;

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof Decimal) {
            return $value;
        }
        return $value !== null ? new Decimal($value) : new Decimal;
    }
}
