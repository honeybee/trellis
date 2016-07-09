<?php

namespace Trellis\EntityType\Attribute\IntegerList;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class IntegerListAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof IntegerList) {
            return $value;
        }

        Assertion::nullOrIsArray($value);

        if (!empty($value) && !$value[0] instanceof Integer) {
            return IntegerList::fromArray($value);
        }
        return $value ? new IntegerList($value) : new IntegerList;
    }
}
