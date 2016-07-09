<?php

namespace Trellis\EntityType\Attribute\TextList;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class TextListAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof TextList) {
            return $value;
        }

        Assertion::nullOrIsArray($value);

        if (!empty($value) && !$value[0] instanceof Text) {
            return TextList::fromArray($value);
        }
        return $value ? new TextList($value) : new TextList;
    }
}
