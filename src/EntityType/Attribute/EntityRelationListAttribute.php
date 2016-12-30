<?php

namespace Trellis\EntityType\Attribute;

use Ds\Vector;
use Trellis\Entity\ValueObject\EntityRelationList;
use Trellis\TypedEntityInterface;
use Trellis\EntityInterface;
use Trellis\EntityTypeInterface;
use Trellis\EntityType\Attribute;
use Trellis\EntityType\EntityTypeMap;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Error\UnexpectedValue;

final class EntityRelationListAttribute extends Attribute
{
    public const PARAM_TYPES = EntityRelationAttribute::PARAM_TYPES;

    /**
     * @var EntityRelationAttribute $internal_attribute
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
        $this->internal_attribute = new EntityRelationAttribute($name, $entity_type, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        switch (true) {
            case $value instanceof EntityRelationList:
                return $value;
            case is_array($value):
                return new EntityRelationList($this->makeRelationalEntities($value, $parent));
            case is_null($value):
                return new EntityRelationList;
            default:
                throw new UnexpectedValue("Trying to create EntityRelationList from non-supported value.");
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
     * @param array $relations
     * @param TypedEntityInterface $parent_entity
     *
     * @return Vector
     */
    private function makeRelationalEntities(array $relations, TypedEntityInterface $parent_entity = null): Vector
    {
        $entities = new Vector;
        foreach ($relations as $entity_relation) {
            $entities->push($this->internal_attribute->makeValue($entity_relation, $parent_entity));
        }
        return $entities;
    }
}
