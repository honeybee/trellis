<?php

namespace Trellis\Runtime\Attribute\EmbeddedEntityList;

use Trellis\Runtime\Entity\EntityList;
use Trellis\Runtime\ValueHolder\ValueHolder;

/**
 * Holds a list of entities as an EntityList.
 */
class EmbeddedEntityListValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param EntityList $other_value list of entities
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        if (!$other_value instanceof EntityList) {
            return false;
        }

        $entities = $this->getValue();

        if (count($entities) !== count($other_value)) {
            return false;
        }

        foreach ($entities as $index => $entity) {
            if (!$entity->isEqualTo($other_value->getItem($index))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        if ($this->valueEquals($this->getAttribute()->getNullValue())) {
            return [];
        }

        $entities = [];

        foreach ($this->getValue() as $entity) {
            $entities[] = $entity->toNative();
        }

        return $entities;
    }

    /**
     * Returns the type of the value that is returned for the toNative() call.
     * This is used for typehints in code generation and might be used in other
     * layers (e.g. web form submissions) to prune empty values from array
     * request parameters (when this method returns 'array'), e.g. "foo[bar][]"
     * as checkboxes in a form will contain empty values for unchecked
     * checkboxes. To know the native type is helpful to handle such a case
     * as the validation rule can't distinguish between deliberate and wrongly
     * given empty strings.
     *
     * @return string return type of the toNative() method
     */
    public function getNativeType()
    {
        return 'array';
    }

    /**
     * Returns the type of the internal value of the valueholder. This can
     * be anything from 'string', 'array' or 'int' to a fully qualified class
     * name of the value object or PHP object used for storage internally.
     *
     * The returned type is the one returned by getValue() method calls.
     *
     * @return string type or FQCN of the internal value
     */
    public function getValueType()
    {
        return '\\' . EntityList::CLASS;
    }
}
