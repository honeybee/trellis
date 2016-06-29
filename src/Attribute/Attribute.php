<?php

namespace Trellis\Attribute;

use Trellis\Entity\EntityTypeInterface;
use Trellis\Value\Any;

class Attribute implements AttributeInterface
{
    /**
     * @var string $name Holds the attribute's name.
     */
    protected $name;

    /**
     * @var EntityTypeInterface $entity_type Holds a reference to the attribute's entity_type.
     */
    protected $entity_type;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     */
    public function __construct($name, EntityTypeInterface $entity_type)
    {
        $this->name = $name;
        $this->entity_type = $entity_type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType()
    {
        return $this->entity_type;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->getType()->getParent();
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return AttributePath::getAttributePath($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getRootEntityType()
    {
        return AttributePath::getRootEntityType($this);
    }

    /**
     * {@inheritdoc}
     */
    public function createValue($value)
    {
        return new Any($value);
    }
}
