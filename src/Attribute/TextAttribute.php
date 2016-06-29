<?php

namespace Trellis\Attribute;

use Trellis\Value\Text;

class TextAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue($value)
    {
        return new Text($value);
    }
}
