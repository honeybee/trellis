<?php

namespace Trellis\Attribute\Text;

use Assert\Assertion;
use Trellis\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class TextAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        Assertion::nullOrString($value);

        return $value ? new Text($this, $value) : new Text($this);
    }
}
