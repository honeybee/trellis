<?php

namespace Trellis\Attribute\Uuid;

use Assert\Assertion;
use Trellis\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class UuidAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        Assertion::nullOrString($value);

        return $value ? new Uuid($this, $value) : new Uuid($this);
    }
}
