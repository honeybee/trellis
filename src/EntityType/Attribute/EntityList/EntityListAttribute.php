<?php

namespace Trellis\EntityType\Attribute\EntityList;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Attribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\EntityType\EntityTypeMap;
use Trellis\Entity\EntityInterface;
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
     * @param mixed[] $options
     */
    public function __construct($name, EntityTypeInterface $entity_type, array $options = [])
    {
        parent::__construct($name, $entity_type, $options);

        if (!$this->options->has('entity_types')) {
            throw new Exception("Missing required options: 'entity_types'.");
        }
        $this->entity_type_map = $this->createEntityTypeMap($this->options->get('entity_types'));
    }

    /**
     * {@inheritdoc}
     */
    public function createValue($value = null, EntityInterface $parent = null)
    {
        if ($value instanceof EntityList) {
            return $value;
        }

        Assertion::nullOrIsArray($value);

        if ($parent && !empty($value) && !$value[0] instanceof EntityInterface) {
            return EntityList::fromNative($value, $this->getEntityTypeMap(), $parent);
        }
        return $value ? new EntityList($value) : new EntityList;
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
            $entity_type = new $entity_type_class($this);
            $entity_types[$entity_type->getPrefix()] = $entity_type;
        }

        return new EntityTypeMap($entity_types);
    }
}
