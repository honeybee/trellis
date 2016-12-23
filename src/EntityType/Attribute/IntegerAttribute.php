<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Integer;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;

final class IntegerAttribute implements AttributeInterface
{
    use AttributeTrait;

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof Integer) {
            return $value;
        }
        return $value !== null ? new Integer($value) : new Integer;
    }
}
