<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Email;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class EmailAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof Email:
                return $value;
            case is_string($value):
                return new Email($value);
            case is_null($value):
                return new Email;
            default:
                throw new UnexpectedValue("Trying to make Email from invalid value-type.");
        }
    }
}
