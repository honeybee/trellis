<?php

namespace Trellis\EntityType\Attribute\ImageList;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Attribute;
use Trellis\Entity\EntityInterface;

class ImageListAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof ImageList) {
            return $value;
        }

        Assertion::nullOrIsArray($value);

        if (!empty($value) && !$value[0] instanceof Image) {
            return ImageList::fromArray($value);
        }
        return $value ? new ImageList($value) : new ImageList;
    }
}
