<?php

namespace Trellis\EntityType\Attribute\Token;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class TokenAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value = null, EntityInterface $parent = null)
    {
        if ($value instanceof Token) {
            return $value;
        }
        return $value !== null ? new Token($value) : new Token;
    }
}
