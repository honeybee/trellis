<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityType\Attribute\EntityList\EntityListAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\EntityType\Options;
use Trellis\EntityType\Path\TypePath;
use Trellis\EntityType\Path\TypePathPart;
use Trellis\Exception;

abstract class Attribute implements AttributeInterface
{
    protected static $reserved_names = [ 'entity_type', 'entity_parent', 'entity_root' ];

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
        if (in_array($name, self::$reserved_names)) {
            throw new Exception(
                "The given name may not be used as it is amoungst the following reserved attribute_names: "
                . implode(', ', self::$reserved_names)
            );
        }

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
        return $this->getEntityType()->getParentAttribute();
    }

    /**
     * {@inheritdoc}
     */
    public function toTypePath()
    {
        $current_attribute = $this->getParent();
        $current_type = $this->getEntityType();
        $path_leaf = new TypePathPart($this->getName());

        $type_path = new TypePath([ $path_leaf ]);
        while ($current_attribute) {
            $type_path = $type_path->push(new TypePathPart($current_attribute->getName(), $current_type->getPrefix()));
            if ($current_attribute = $current_attribute->getParent()) {
                $current_type = $current_attribute->getEntityType();
            }
        }
        $type_path = $type_path->getSize() > 1 ? $type_path->reverse() : $type_path;

        return  (string)$type_path;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param boolean $fluent
     *
     * @return mixed|Options
     */
    public function getOption($key, $default = null, $fluent = false)
    {
        return $this->options->get($key, $default, $fluent);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasOption($key)
    {
        return $this->options->has($key);
    }
}
