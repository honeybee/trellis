<?php

namespace Trellis\Attribute\Decimal;

use Trellis\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class DecimalAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        return $value !== null ? new Decimal($value) : new Decimal;
    }
}
