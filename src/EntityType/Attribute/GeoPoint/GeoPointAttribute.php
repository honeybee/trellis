<?php

namespace Trellis\EntityType\Attribute\GeoPoint;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class GeoPointAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof GeoPoint) {
            return $value;
        }
        return $value !== null ? GeoPoint::fromArray($value) : new GeoPoint;
    }
}
