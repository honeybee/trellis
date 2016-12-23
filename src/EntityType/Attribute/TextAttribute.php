<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Text;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;

final class TextAttribute implements AttributeInterface
{
    use AttributeTrait;

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof Text) {
            return $value;
        }
        return $value !== null ? new Text($value) : new Text;
    }
}
