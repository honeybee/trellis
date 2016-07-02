<?php

namespace Trellis\Attribute\Uuid;

use Trellis\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class UuidAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        return $value ? new Uuid($this, $value) : new Uuid($this);
    }
}
