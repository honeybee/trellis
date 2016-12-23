<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Boolean;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;

final class BooleanAttribute implements AttributeInterface
{
    use AttributeTrait;

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof Boolean) {
            return $value;
        }
        return $value !== null ? new Boolean($value) : new Boolean;
    }
}
