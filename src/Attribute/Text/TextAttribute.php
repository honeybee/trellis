<?php

namespace Trellis\Attribute\Text;

use Trellis\Attribute\Attribute;

class TextAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value = null)
    {
        return $value ? new Text($value) : new Text;
    }
}
