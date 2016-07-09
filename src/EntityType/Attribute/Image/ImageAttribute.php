<?php

namespace Trellis\EntityType\Attribute\Image;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class ImageAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof Image) {
            return $value;
        }
        return $value !== null ? new Image($value) : new Image;
    }
}
