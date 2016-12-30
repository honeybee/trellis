<?php

namespace Trellis\Entity;

use Trellis\Error\InvalidType;

abstract class NestedEntity extends Entity implements ValueObjectInterface
{
    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        foreach ($this->getValueObjectMap() as $value) {
            if (!$value->isEmpty()) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param ValueObjectInterface $other_value
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        if (!$other_value instanceof static) {
            throw new InvalidType(
                "Trying to compare two entities(values) of different types. ".
                "Expecting instance of ".static::CLASS.
                ", but instead an instance of ".get_class($other_value)." was given."
            );
        }
        foreach ($this->getValueObjectMap() as $attr_name => $value) {
            if (!$value->equals($other_value->get($attr_name))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf("%s:%s", $this->getEntityType()->getName(), $this->getIdentity());
    }
}
