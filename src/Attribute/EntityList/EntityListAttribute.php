<?php

namespace Trellis\Runtime\Attribute\EmbeddedEntityList;

/**
 * Allows to nest multiple entity-types below a defined attribute_name.
 * Pass in the 'OPTION_ENTITY_TYPES' option to define the types you would like to nest.
 * The corresponding value-structure is organized as a collection of entities.
 *
 * Supported options: OPTION_ENTITY_TYPES
 */
class EntityListAttribute extends Attribute
{
    /**
     * An array holding the embed-type instances supported by a specific embed-attribute instance.
     *
     * @var array
     */
    protected $entity_type_map = null;

    public function __construct($name, EntityTypeInterface $entity_type)
    {
        $this->entity_type_map = $this->createEmbeddedTypeMap();

        parent::__construct($name, $entity_type);
    }

    protected function createEmbeddedTypeMap()
    {
        $entity_types = [];
        foreach ($this->getOption(self::OPTION_ENTITY_TYPES) as $entity_type_class) {
            if (!class_exists($entity_type_class)) {
                throw new RuntimeException(
                    sprintf('Unable to load configured "entity_type" class called %s.', $entity_type_class)
                );
            }
            $embedded_type = new $entity_type_class($this->getType(), $this);
            $entity_types[$embedded_type->getPrefix()] = $embedded_type);
        }

        return new EntityTypeMap($entity_types);
    }

    /**
     * Returns the embed-types as an array.
     *
     * @return array
     */
    public function getEmbeddedEntityTypeMap()
    {
        return $this->entity_type_map;
    }

    public function getEmbeddedTypeByPrefix($prefix)
    {
        if ($this->getEmbeddedEntityTypeMap()->hasKey($prefix)) {
            return $this->getEmbeddedEntityTypeMap()->getItem($prefix);
        }

        return null;
    }

    public function getEmbeddedTypeByClassName($class_name)
    {
        $found_types = $this->getEmbeddedEntityTypeMap()->filter(
            function (EntityTypeInterface $entity_type) use ($class_name) {
                return get_class($entity_type) === $class_name;
            }
        )->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }

    public function getEmbeddedTypeByName($name)
    {
        $found_types = $this->getEmbeddedEntityTypeMap()->filter(
            function ($entity_type) use ($name) {
                return $entity_type === $name;
            }
        )->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }
}
