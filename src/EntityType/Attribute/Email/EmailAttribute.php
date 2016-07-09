<?php

namespace Trellis\EntityType\Attribute\Email;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class EmailAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof Email) {
            return $value;
        }
        return $value !== null ? new Email($value) : new Email;
    }
}
