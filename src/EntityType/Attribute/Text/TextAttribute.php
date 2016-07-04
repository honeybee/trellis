<?php

namespace Trellis\EntityType\Attribute\Text;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class TextAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        return $value !== null ? new Text($value) : new Text;
    }
}
