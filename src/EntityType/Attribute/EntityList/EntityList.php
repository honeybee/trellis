<?php

namespace Trellis\EntityType\Attribute\EntityList;

use Trellis\Collection\TypedList;
use Trellis\EntityType\EntityTypeMap;
use Trellis\Entity\EntityInterface;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Exception;

/**
 * Holds a list of entities as an EntityList.
 */
class EntityList extends TypedList implements ValueInterface
{
    /**
     * Creates a new entity-list from the given native representation.
     *
     * @param mixed[] $data
     * @param EntityTypeMap $type_map
     * @param EntityInterface $parent
     *
     * @return EntityList
     */
    public static function fromNative(array $data, EntityTypeMap $type_map, EntityInterface $parent)
    {
        $entities = [];
        foreach ($data as $entity_data) {
            if (!isset($entity_data[$parent::TYPE_KEY])) {
                throw new Exception("Missing required '".$parent::TYPE_KEY."' key within given entity-data.");
            }

            $type_prefix = $entity_data[$parent::TYPE_KEY];
            if (!$type_map->hasKey($type_prefix)) {
                throw new Exception(
                    "Unable to resolve given ".$parent::TYPE_KEY."='$entity_preifx' to an known entity-type."
                );
            }
            unset($entity_data[$parent::TYPE_KEY]);
            $entity_type = $type_map->getItem($type_prefix);
            $entities[] = $entity_type->createEntity($entity_data, $parent);
        }

        return new EntityList($entities);
    }

    /**
     * @param EntityInterface[] $entities
     */
    public function __construct(array $entities = [])
    {
        parent::__construct(EntityInterface::CLASS, $entities);
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(ValueInterface $other_value)
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
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->toArray();
    }
}
