<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Boolean;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class BooleanAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof Boolean:
                return $value;
            case is_bool($value):
                return new Boolean($value);
            case is_null($value):
                return new Boolean;
            default:
                throw new UnexpectedValue("Trying to make Boolean from invalid value-type.");
        }
    }
}
