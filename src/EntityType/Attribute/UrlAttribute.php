<?php

namespace Trellis\EntityType\Attribute;

use Trellis\Entity\ValueObject\Url;
use Trellis\Entity\ValueObjectInterface;
use Trellis\EntityInterface;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class UrlAttribute extends Attribute
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
            case $value instanceof Url:
                return $value;
            case is_string($value):
                return new Url($value);
            case is_null($value):
                return new Url;
            default:
                throw new UnexpectedValue("Trying to make Url from invalid value-type.");
        }
    }
}
