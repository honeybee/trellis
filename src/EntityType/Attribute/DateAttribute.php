<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Date;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class DateAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof Date:
                return $value;
            case is_string($value):
                return Date::createFromString($value);
            case is_null($value):
                return new Date;
            default:
                throw new UnexpectedValue("Trying to make Date from invalid value-type.");
        }
    }
}
