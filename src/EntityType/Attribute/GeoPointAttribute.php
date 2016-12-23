<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\GeoPoint;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class GeoPointAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof GeoPoint:
                return $value;
            case is_array($value):
                return GeoPoint::fromArray($value);
            case is_null($value):
                return new GeoPoint;
            default:
                throw new UnexpectedValue("Trying to make GeoPoint from invalid value.");
        }
    }
}
