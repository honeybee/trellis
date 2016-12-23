<?php

namespace Trellis\EntityType\Attribute;

use Ds\Vector;
use Trellis\DomainEntityInterface;
use Trellis\EntityInterface;
use Trellis\EntityTypeInterface;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeTrait;
use Trellis\EntityType\EntityTypeMap;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\EntityList;
use Trellis\Error\CorruptValues;
use Trellis\Error\InvalidType;
use Trellis\Error\MissingImplementation;

final class EntityListAttribute implements AttributeInterface
{
    const OPTION_TYPES = 'entity_types';

    use AttributeTrait {
        AttributeTrait::__construct as private __init;
    }

    /**
     * @var EntityTypeMap $entity_type_map
     */
    private $entity_type_map;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     * @param mixed[] $params
     */
    public function __construct(string $name, EntityTypeInterface $entity_type, array $params = [])
    {
        $this->__init($name, $entity_type, $params);
        $this->entity_type_map = $this->makeEntityTypeMap($this->getOption('entity_types', []));
    }

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof EntityList:
                return $value;
            case is_array($value):
                return new EntityList($this->makeEntities($value, $parent));
            case is_null($value):
                return new EntityList;
            default:
                throw new InvalidType("Trying to create entity-list from non-supported value.");
        }
    }

    /**
     * @return EntityTypeMap
     */
    public function getEntityTypeMap(): EntityTypeMap
    {
        return $this->entity_type_map;
    }

    /**
     * @param string[] $class_map
     *
     * @return EntityTypeMap
     */
    private function makeEntityTypeMap(array $class_map): EntityTypeMap
    {
        $entity_types = new Vector;
        foreach ($class_map as $entity_type_class) {
            if (!class_exists($entity_type_class)) {
                throw new MissingImplementation("Unable to load given entity-type class: '$entity_type_class'");
            }
            $entity_types->push(new $entity_type_class($this));
        }
        return new EntityTypeMap($entity_types);
    }

    /**
     * @param array $values
     * @param DomainEntityInterface $parent_entity
     *
     * @return Vector
     */
    private function makeEntities(array $values, DomainEntityInterface $parent_entity = null): Vector
    {
        $entities = new Vector;
        foreach ($values as $entity_values) {
            if (!isset($entity_values[DomainEntityInterface::ENTITY_TYPE])) {
                throw new CorruptValues("Missing required @type key within given entity values.");
            }
            $type_prefix = $entity_values[DomainEntityInterface::ENTITY_TYPE];
            if (!$this->entity_type_map->has($type_prefix)) {
                throw new CorruptValues("Unknown type referenced from with given entity values.");
            }
            $entities->push(
                $this->entity_type_map->get($type_prefix)->makeEntity($entity_values, $parent_entity)
            );
        }
        return $entities;
    }
}
