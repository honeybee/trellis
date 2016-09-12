<?php

namespace Trellis\Runtime\Attribute\EmbeddedEntityList;

use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListRule;
use Trellis\Runtime\Attribute\ListAttribute;
use Trellis\Runtime\Entity\EntityList;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\EntityTypeInterface;
use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\EntityTypeMap;
use Trellis\Common\Error\RuntimeException;

/**
 * Allows to nest multiple types below a defined attribute_name.
 * Pass in the 'OPTION_ENTITY_TYPES' option to define the types you would like to nest.
 * The corresponding value-structure is organized as a collection of entities.
 *
 * Supported options: OPTION_ENTITY_TYPES
 */
class EmbeddedEntityListAttribute extends ListAttribute
{
    /**
     * Option that holds an array of supported entity-type names.
     */
    const OPTION_ENTITY_TYPES = EmbeddedEntityListRule::OPTION_ENTITY_TYPES;

    /**
     * An array holding the embed-type instances supported by a specific embed-attribute instance.
     *
     * @var array
     */
    protected $entity_type_map = null;

    public function __construct(
        $name,
        EntityTypeInterface $type,
        array $options = [],
        AttributeInterface $parent = null
    ) {
        parent::__construct($name, $type, $options, $parent);

        $this->entity_type_map = $this->createEmbeddedTypeMap();
    }

    protected function createEmbeddedTypeMap()
    {
        $entity_type_map = new EntityTypeMap();
        foreach ($this->getOption(self::OPTION_ENTITY_TYPES) as $embedded_type_class) {
            if (!class_exists($embedded_type_class)) {
                throw new RuntimeException(
                    sprintf('Unable to load configured "embedded_entity_type" class called %s.', $embedded_type_class)
                );
            }
            $embedded_type = new $embedded_type_class($this->getType(), $this);
            $entity_type_map->setItem($embedded_type->getPrefix(), $embedded_type);
        }

        return $entity_type_map;
    }

    /**
     * Returns an attribute's null value.
     *
     * @return mixed value to be used/interpreted as null (not set)
     */
    public function getNullValue()
    {
        return new EntityList();
    }

    public function getDefaultValue()
    {
        return $this->getNullValue();
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
        $type_map = $this->getEmbeddedEntityTypeMap();
        if ($type_map->hasKey($prefix)) {
            return $type_map->getItem($prefix);
        }
        $aliases = (array)$this->getOption('type_aliases', []);
        if (isset($aliases[$prefix])) {
            $alias = $aliases[$prefix];
        }
        return $type_map->hasKey($alias) ? $type_map->getItem($alias) : null;
    }

    public function getEmbeddedTypeByClassName($class_name)
    {
        $found_types = $this->getEmbeddedEntityTypeMap()->filter(
            function ($entity_type) use ($class_name) {
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

    /**
     * Return a list of rules used to validate a specific attribute instance's value.
     *
     * @return RuleList
     */
    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();
        $options[self::OPTION_ENTITY_TYPES] = $this->getEmbeddedEntityTypeMap();

        $rules->push(
            new EmbeddedEntityListRule('valid-embedded-entity-list-data', $options)
        );

        return $rules;
    }
}
