<?php

namespace Trellis\EntityType\Attribute\Url;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class UrlAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof Url) {
            return $value;
        }
        return $value !== null ? new Url($value) : new Url;
    }
}
