<?php

namespace Trellis\EntityType\Attribute\Integer;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class IntegerAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        return $value !== null ? new Integer($value) : new Integer;
    }
}
