<?php

namespace Trellis\EntityType\Attribute\Boolean;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class BooleanAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof Boolean) {
            return $value;
        }
        return $value !== null ? new Boolean($value) : new Boolean;
    }
}
