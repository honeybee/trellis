<?php

namespace Trellis\EntityType\Attribute;

use Trellis\Assert\Assertion;
use Trellis\Entity\ValueObject\Nil;
use Trellis\Entity\ValueObjectInterface;
use Trellis\EntityInterface;
use Trellis\EntityRelationInterface;
use Trellis\EntityType\Attribute;
use Trellis\EntityType\EntityTypeMap;
use Trellis\EntityTypeInterface;

final class EntityRelationAttribute extends Attribute
{
    public const PARAM_TYPES = NestedEntityAttribute::PARAM_TYPES;

    /**
     * @var NestedEntityAttribute $internal_attribute
     */
    private $internal_attribute;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     * @param array $params
     */
    public function __construct($name, EntityTypeInterface $entity_type, $params = [])
    {
        parent::__construct($name, $entity_type, $params);
        $this->internal_attribute = new NestedEntityAttribute($name, $entity_type, $params);
    }

    /**
     * @return EntityTypeMap
     */
    public function getEntityTypeMap(): EntityTypeMap
    {
        return $this->internal_attribute->getEntityTypeMap();
    }

    /**
     * @param mixed $value
     * @param EntityInterface $parent The entity that the value is being created for.
     *
     * @return ValueObjectInterface
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if (is_null($value)) {
            return new Nil;
        }
        $entity = $this->internal_attribute->makeValue($value, $parent);
        Assertion::isInstanceOf($entity, EntityRelationInterface::CLASS);
        return $entity;
    }
}
