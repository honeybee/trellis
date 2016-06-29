<?php

namespace Trellis\Attribute;

use Trellis\Value\Uuid;

class UuidAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value)
    {
        return new Uuid($value);
    }
}
