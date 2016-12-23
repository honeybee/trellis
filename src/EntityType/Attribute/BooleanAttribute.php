<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Boolean;
use Trellis\EntityType\Attribute;

final class BooleanAttribute extends Attribute
{
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
