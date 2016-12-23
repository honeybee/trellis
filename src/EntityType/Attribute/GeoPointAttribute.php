<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\GeoPoint;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;

final class GeoPointAttribute implements AttributeInterface
{
    use AttributeTrait;

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof GeoPoint) {
            return $value;
        }
        return $value !== null ? new GeoPoint($value) : new GeoPoint;
    }
}
