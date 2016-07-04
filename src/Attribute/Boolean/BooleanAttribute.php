<?php

namespace Trellis\Attribute\Boolean;

use Assert\Assertion;
use Trellis\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class BooleanAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        return $value !== null ? new Boolean($value) : new Boolean;
    }
}
