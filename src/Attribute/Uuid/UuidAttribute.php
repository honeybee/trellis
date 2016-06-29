<?php

namespace Trellis\Attribute\Uuid;

use Trellis\Attribute\Attribute;

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
