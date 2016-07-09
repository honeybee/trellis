<?php

namespace Trellis\EntityType\Attribute\EntityList;

use Trellis\Collection\TypedList;
use Trellis\Collection\UniqueItemInterface;
use Trellis\EntityType\EntityTypeMap;
use Trellis\Entity\EntityInterface;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Exception;

/**
 * Holds a list of entities as an EntityList.
 */
class EntityList extends TypedList implements ValueInterface, UniqueItemInterface
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
            if (!isset($entity_data[$parent::ENTITY_TYPE])) {
                throw new Exception("Missing required '".$parent::ENTITY_TYPE."' key within given entity-data.");
            }

            $type_prefix = $entity_data[$parent::ENTITY_TYPE];
            if (!$type_map->hasKey($type_prefix)) {
                throw new Exception(
                    "Unable to resolve given ".$parent::ENTITY_TYPE."='$type_prefix' to an known entity-type."
                );
            }
            unset($entity_data[$parent::ENTITY_TYPE]);
            $entity_type = $type_map->getItem($type_prefix);
            $entities[] = $entity_type->createEntity($entity_data, $parent);
        }

        return new static($entities);
    }

    /**
     * @param EntityInterface[] $entities
     */
    public function __construct(array $entities = [], $allowed_subclass = null)
    {
        parent::__construct($allowed_subclass ?: EntityInterface::CLASS, $entities);
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(ValueInterface $righthand_list)
    {
        if (!$righthand_list instanceof EntityList || $this->getSize() !== $righthand_list->getSize()) {
            return false;
        }
        foreach ($this->items as $pos => $lefthand_entity) {
            if (!$lefthand_entity->isEqualTo($righthand_list->getItem($pos))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param EntityList $righthand_list
     *
     * @return EntityList
     */
    public function diff(EntityList $righthand_list)
    {
        $different_entities = [];
        foreach ($this->items as $pos => $lefthand_entity) {
            $righthand_entity = $righthand_list->getItem($pos);
            if (!$righthand_entity || $lefthand_entity->getEntityType() !== $righthand_entity->getEntityType()) {
                $different_entities[] = $lefthand_entity;
                continue;
            }
            $diff = $lefthand_entity->diff($righthand_entity);
            if ($diff->getSize() > 0) {
                $different_entities[] = $lefthand_entity;
            }
        }

        $copy = clone $this;
        $copy->items = $different_entities;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->toArray();
    }
}
