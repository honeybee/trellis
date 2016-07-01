<?php

namespace Trellis\Attribute\Uuid;

use Trellis\Attribute\Attribute;

class UuidAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value = null)
    {
        return $value ? new Uuid($value) : new Uuid;
    }
}
