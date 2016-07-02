<?php

namespace Trellis\Attribute\EntityList;

use Assert\Assertion;
use Trellis\Attribute\Attribute;
use Trellis\Entity\EntityInterface;
use Trellis\Entity\EntityTypeInterface;
use Trellis\Entity\EntityTypeMap;
use Trellis\Exception;

/**
 * Allows to nest multiple entity-types below a defined attribute_name.
 * Pass in the 'OPTION_ENTITY_TYPES' option to define the types you would like to nest.
 * The corresponding value-structure is organized as a collection of entities.
 */
class EntityListAttribute extends Attribute
{
    /**
     * @var EntityTypeMap $entity_type_map
     */
    protected $entity_type_map = null;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     * @param string[] $entity_types
     */
    public function __construct($name, EntityTypeInterface $entity_type, array $entity_types)
    {
        parent::__construct($name, $entity_type);

        $this->entity_type_map = $this->createEntityTypeMap($entity_types);
    }

    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        Assertion::nullOrIsArray($value);

        if (!empty($value) && !$value[0] instanceof EntityInterface) {
            return EntityList::fromNative($value, $this, $parent);
        }
        return $value ? new EntityList($this, $value) : new EntityList($this);
    }

    /**
     * Returns the embed-types as an array.
     *
     * @return EntityTypeMap
     */
    public function getEntityTypeMap()
    {
        return $this->entity_type_map;
    }

    /**
     * @param string $prefix
     *
     * @return EntityTypeInterface
     */
    public function getEntityTypeByPrefix($prefix)
    {
        return $this->entity_type_map->hasKey($prefix) ? $this->entity_type_map->getItem($prefix) : null;
    }

    /**
     * @param string $class
     *
     * @return EntityTypeInterface
     */
    public function getEntityTypeByClass($class)
    {
        $found_types = $this->entity_type_map->filter(
            function (EntityTypeInterface $entity_type) use ($class) {
                return get_class($entity_type) === $class;
            }
        )->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }

    /**
     * @param string $name
     *
     * @return EntityTypeInterface
     */
    public function getEntityTypeByName($name)
    {
        $found_types = $this->entity_type_map->filter(
            function ($entity_type) use ($name) {
                return $entity_type === $name;
            }
        )->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }

    /**
     * @param string[] $class_map
     *
     * @return EntityTypeMap
     */
    protected function createEntityTypeMap(array $class_map)
    {
        $entity_types = [];
        foreach ($class_map as $entity_type_class) {
            if (!class_exists($entity_type_class)) {
                throw new Exception("Unable to load given entity-type class: '$entity_type_class'");
            }
            $entity_type = new $entity_type_class($this->getEntityType(), $this);
            $entity_types[$entity_type->getPrefix()] = $entity_type;
        }

        return new EntityTypeMap($entity_types);
    }
}
