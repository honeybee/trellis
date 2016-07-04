<?php

namespace Trellis\EntityType\Attribute;

use Shrink0r\Monatic\Maybe;
use Shrink0r\Monatic\None;
use Trellis\EntityType\Attribute\EntityList\EntityListAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\EntityType\Path\TypePath;

abstract class Attribute implements AttributeInterface
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
     * @var mixed[] $options
     */
    protected $options;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     * @param mixed[] $options
     */
    public function __construct($name, EntityTypeInterface $entity_type, array $options = [])
    {
        $this->name = $name;
        $this->entity_type = $entity_type;
        $this->options = new Options($options);
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
    public function toTypePath()
    {
        $path_parts = [ $this->getName() ];
        $current_attribute = $this->getParent();
        $current_type = $this->getType();

        while ($current_attribute instanceof EntityListAttribute) {
            array_push($path_parts, $current_type->getPrefix(), $current_attribute->getName());
            $current_type = $current_attribute->getType();
            $current_attribute = $current_attribute->getParent();
        }
        $type_path = new TypePath($path_parts);

        return (string)$type_path->reverse();
    }

    /**
     * {@inheritdoc}
     */
    public function getRootEntityType()
    {
        $root_type = $attribute->getType()->getRoot();

        return $root_type ? $root_type : $attribute->getType();
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param boolean $fluent
     *
     * @return mixed|Maybe
     */
    public function getOption($key, $default = null, $fluent = false)
    {
        $value = $this->options->{$key} instanceof None ? Maybe::unit($default) : $this->options->{$key};

        return $fluent ? $value : $value->get();
    }
}
