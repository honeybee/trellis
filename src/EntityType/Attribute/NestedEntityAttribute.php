<?php

namespace Trellis\EntityType\Attribute;

use Ds\Vector;
use Trellis\Assert\Assertion;
use Trellis\Entity\NestedEntity;
use Trellis\Entity\ValueObject\Nil;
use Trellis\Entity\ValueObjectInterface;
use Trellis\EntityInterface;
use Trellis\EntityType\Attribute;
use Trellis\EntityType\EntityTypeMap;
use Trellis\EntityTypeInterface;
use Trellis\Error\CorruptValues;
use Trellis\Error\MissingImplementation;
use Trellis\Error\UnexpectedValue;
use Trellis\TypedEntityInterface;

final class NestedEntityAttribute extends Attribute
{
    public const PARAM_TYPES = "entity_types";

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
        parent::__construct($name, $entity_type, $params);
        $this->entity_type_map = $this->makeEntityTypeMap($this->getParam(self::PARAM_TYPES, []));
    }

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof NestedEntity:
                return $value;
            case is_array($value):
                return $this->makeEntity($value, $parent);
            case is_null($value):
                return new Nil;
            default:
                throw new UnexpectedValue("Trying to create NestedEntity from non-supported value.");
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
     * @param array $entity_values
     * @param TypedEntityInterface|null $parent_entity
     *
     * @return NestedEntity
     */
    private function makeEntity(array $entity_values, TypedEntityInterface $parent_entity = null): NestedEntity
    {
        Assertion::keyExists($entity_values, TypedEntityInterface::ENTITY_TYPE);
        $type_prefix = $entity_values[TypedEntityInterface::ENTITY_TYPE];
        if (!$this->entity_type_map->has($type_prefix)) {
            throw new CorruptValues("Unknown type prefix given within nested-entity values.");
        }
        /* @var NestedEntity $entity */
        $entity = $this->entity_type_map->get($type_prefix)->makeEntity($entity_values, $parent_entity);
        return $entity;
    }
}
