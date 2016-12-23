<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Timestamp;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class TimestampAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof Timestamp:
                return $value;
            case is_string($value):
                return Timestamp::createFromString($value);
            case is_null($value):
                return new Timestamp;
            default:
                throw new UnexpectedValue("Trying to make Date from invalid value-type.");
        }
    }
}
