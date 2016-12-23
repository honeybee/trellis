<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Text;
use Trellis\EntityType\Attribute;
use Trellis\Error\UnexpectedValue;

final class TextAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof Text:
                return $value;
            case is_string($value):
                return new Text($value);
            case is_null($value):
                return new Text;
            default:
                throw new UnexpectedValue("Trying to make Text from invalid value-type.");
        }
    }
}
