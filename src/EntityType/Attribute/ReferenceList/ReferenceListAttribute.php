<?php

namespace Trellis\EntityType\Attribute\ReferenceList;

use Assert\Assertion;
use Trellis\EntityType\Attribute\EntityList\EntityListAttribute;
use Trellis\Entity\EntityInterface;
use Trellis\Entity\ReferenceInterface;

class ReferenceListAttribute extends EntityListAttribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof ReferenceList) {
            return $value;
        }

        Assertion::nullOrIsArray($value);

        if (!empty($value) && !$value[0] instanceof ReferenceInterface) {
            return ReferenceList::fromNative($value, $this->getEntityTypeMap(), $parent);
        }
        return $value ? new ReferenceList($value) : new ReferenceList;
    }
}
