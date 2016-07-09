<?php

namespace Trellis\EntityType\Attribute\Asset;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class AssetAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof Asset) {
            return $value;
        }
        return $value !== null ? Asset::fromArray($value) : new Asset;
    }
}
