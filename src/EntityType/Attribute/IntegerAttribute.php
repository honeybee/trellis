<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Integer;
use Trellis\EntityType\Attribute;

final class IntegerAttribute extends Attribute
{
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
