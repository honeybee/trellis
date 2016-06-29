<?php

namespace Trellis\Attribute\Text;

use Trellis\Attribute\Attribute;

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
