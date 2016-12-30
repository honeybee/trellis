<?php

namespace Trellis\EntityType\Attribute;

use Ds\Vector;
use Trellis\TypedEntityInterface;
use Trellis\EntityInterface;
use Trellis\EntityTypeInterface;
use Trellis\EntityType\Attribute;
use Trellis\EntityType\EntityTypeMap;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\NestedEntityList;
use Trellis\Error\UnexpectedValue;

final class NestedEntityListAttribute extends Attribute
{
    public const PARAM_TYPES = NestedEntityAttribute::PARAM_TYPES;

    /**
     * @var NestedEntityAttribute $internal_attribute
     */
    private $internal_attribute;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     * @param mixed[] $params
     */
    public function __construct(string $name, EntityTypeInterface $entity_type, array $params = [])
    {
        parent::__construct($name, $entity_type, $params);
        $this->internal_attribute = new NestedEntityAttribute($name, $entity_type, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof NestedEntityList:
                return $value;
            case is_array($value):
                return new NestedEntityList($this->makeEntities($value, $parent));
            case is_null($value):
                return new NestedEntityList;
            default:
                throw new UnexpectedValue("Trying to create EntityList from non-supported value.");
        }
    }

    /**
     * @return EntityTypeMap
     */
    public function getEntityTypeMap(): EntityTypeMap
    {
        return $this->internal_attribute->getEntityTypeMap();
    }

    /**
     * @param array $values
     * @param TypedEntityInterface $parent_entity
     *
     * @return Vector
     */
    private function makeEntities(array $values, TypedEntityInterface $parent_entity = null): Vector
    {
        $entities = new Vector;
        foreach ($values as $entity_values) {
            $entities->push($this->internal_attribute->makeValue($entity_values, $parent_entity));
        }
        return $entities;
    }
}
