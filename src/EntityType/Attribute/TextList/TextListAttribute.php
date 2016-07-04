<?php

namespace Trellis\EntityType\Attribute\TextList;

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
        return $value !== null ? new TextList($value) : new TextList;
    }
}
