<?php

namespace Trellis\Attribute\EntityList;

use Trellis\Attribute\AttributeInterface;
use Trellis\Collection\TypedList;
use Trellis\Entity\EntityInterface;
use Trellis\Exception;
use Trellis\Value\HasAttribute;
use Trellis\Value\ValueInterface;

/**
 * Holds a list of entities as an EntityList.
 */
class EntityList extends TypedList implements ValueInterface
{
    use HasAttribute;

    /**
     * Creates a new entity-list from the given native representation.
     *
     * @param mixed[] $data
     * @param EntityListAttribute $attribute
     * @param EntityInterface $parent
     *
     * @return EntityList
     */
    public static function fromNative(array $data, EntityListAttribute $attribute, EntityInterface $parent)
    {
        $type_map = $attribute->getEntityTypeMap();
        $entities = [];
        foreach ($data as $entity_data) {
            if (!isset($entity_data['@type'])) {
                throw new Exception("Missing required '@type' key within given entity-data.");
            }

            $type_prefix = $entity_data['@type'];
            if (!$type_map->hasKey($type_prefix)) {
                throw new Exception("Unable to resolve given @type='$entity_preifx' to an known entity-type.");
            }
            unset($entity_data['@type']);
            $entity_type = $type_map->getItem($type_prefix);
            $entities[] = $entity_type->createEntity($entity_data, $parent);
        }

        return new EntityList($attribute, $entities);
    }

    /**
     * @param AttributeInterface $attribute
     * @param EntityInterface[] $entities
     */
    public function __construct(AttributeInterface $attribute, array $entities = [])
    {
        $this->attribute = $attribute;

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
