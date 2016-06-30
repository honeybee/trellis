<?php

namespace Trellis\Runtime\Attribute\EmbeddedEntityList;

use Trellis\Collection\TypedList;
use Trellis\Entity\EntityInterface;
use Trellis\Runtime\Entity\EntityList;
use Trellis\Value\ValueInterface;

/**
 * Holds a list of entities as an EntityList.
 */
class EntityList extends TypedList implements ValueInterface
{
    public function __construct(array $entity_types)
    {
        parent::__construct(EntityInterface::CLASS, $entity_types);
    }

    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param ValueInterface $other_value
     *
     * @return boolean
     */
    protected function isEqualTo(ValueInterface $other_value)
    {
        if (!$other_value instanceof EntityList) {
            return false;
        }
        if ($this->getSize() !== $other_value->getSize()) {
            return false;
        }

        foreach ($this->items as $index => $entity) {
            if (!$entity->isEqualTo($other_value->getItem($index))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        return $this->toArray();
    }
}
