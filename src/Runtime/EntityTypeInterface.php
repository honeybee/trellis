<?php
namespace Trellis\Runtime;

use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Attribute\AttributeMap;
use Trellis\Runtime\Entity\EntityInterface;

/**
 * EntityTypeInterfaces define data structures by composing property related strategies named AttributeInterface,
 * to derive concrete instances of the defined data structures in form of EntityInterface's.
 */
interface EntityTypeInterface
{
    /**
     * Returns the name of the type.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the type's prefix (technical identifier).
     *
     * @return string
     */
    public function getPrefix();

    /**
     * Returns the type's parent, if it has one.
     *
     * @return EntityTypeInterface|null
     */
    public function getParent();

    /**
     * Returns the type's root-parent, if it has one.
     *
     * @return EntityTypeInterface|null
     */
    public function getRoot();

    /**
     * Tells if an entity-type is the root-type of an entity aggregate.
     *
     * @return boolean
     */
    public function isRoot();

    /**
     * Returns the type's parent-attribute, if it has one.
     *
     * @return AttributeInterface|null
     */
    public function getParentAttribute();

    /**
     * Returns the type's attribute map.
     *
     * @param array $attribute_names Optional list of attribute names to filter for.
     * @param array $types Optional list of attribute types to filter for.
     *
     * @return AttributeMap
     */
    public function getAttributes(array $attribute_names = [], array $types = []);

    /**
     * Returns a certain type attribute by name.
     *
     * @param string $name
     *
     * @return AttributeInterface
     */
    public function getAttribute($name);

    /**
     * @param string $attribute_name the name of the attribute that might exist on the type
     *
     * @return bool whether the attribute is defined on the type
     */
    public function hasAttribute($attribute_name);

    /**
     * Creates a new EntityInterface instance.
     *
     * @param array $data Optional data for initial hydration.
     * @param EntityInterface $parent_entity
     *
     * @return EntityInterface
     */
    public function createEntity(array $data = [], EntityInterface $parent_entity = null);

    /**
     * Returns the class(name) to use when creating new entries for this type.
     *
     * @return string
     */
    public static function getEntityImplementor();

    public function getDefaultAttributeNames();

    public function getDefaultAttributes();

    public function getAttributeByPath($attribute_path);
}
