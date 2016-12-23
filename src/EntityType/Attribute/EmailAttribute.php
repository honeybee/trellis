<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityInterface;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Email;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;

final class EmailAttribute implements AttributeInterface
{
    use AttributeTrait;

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof Email) {
            return $value;
        }
        return $value !== null ? new Email($value) : new Email;
    }
}
