<?php

namespace Trellis\EntityType\Attribute;

use Trellis\Entity\ValueObject\Uuid;
use Trellis\Entity\ValueObjectInterface;
use Trellis\EntityInterface;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class UuidAttribute extends Attribute
{
    /**
     * @param mixed $value
     * @param EntityInterface $parent The entity that the value is being created for.
     *
     * @return ValueObjectInterface
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof Uuid:
                return $value;
            case is_string($value):
                return new Uuid($value);
            case is_null($value):
                return new Uuid;
            default:
                throw new UnexpectedValue("Trying to make Uuid from invalid value-type.");
        }
    }
}
