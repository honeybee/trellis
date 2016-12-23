<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Decimal;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class DecimalAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof Decimal:
                return $value;
            case is_float($value):
                return new Decimal($value);
            case is_null($value):
                return new Decimal;
            default:
                throw new UnexpectedValue("Trying to make Text from invalid value-type.");
        }
    }
}
