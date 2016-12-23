<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Integer;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class IntegerAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof Integer:
                return $value;
            case is_int($value):
                return new Integer($value);
            case is_null($value):
                return new Integer;
            default:
                throw new UnexpectedValue("Trying to make Integer from invalid value-type.");
        }
    }
}
