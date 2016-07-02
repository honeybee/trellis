<?php

namespace Trellis\Attribute\Text;

use Trellis\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class TextAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        return $value ? new Text($this, $value) : new Text($this);
    }
}
