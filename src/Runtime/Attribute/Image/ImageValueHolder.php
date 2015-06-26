<?php

namespace Trellis\Runtime\Attribute\Image;

use Trellis\Runtime\ValueHolder\ValueHolder;

class ImageValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param mixed $other_value Image or acceptable array
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        $value = $this->getValue();

        if (is_array($other_value)) {
            return $value->similarToArray($other_value);
        } elseif ($other_value instanceof Image) {
            return $value->similarTo($other_value);
        }

        // else

        return false;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return array value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        if (!$this->getValue() instanceof Image) {
            return '';
        }

        return $this->getValue()->toNative();
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
     * Returns the type of the internal value of the value holder. This can
     * be anything from 'string', 'array' or 'int' to fully qualified class
     * names of value objects or PHP objects used for storage internally.
     *
     * @return string type or FQCN of the internal value
     */
    public function getValueType()
    {
        return Image::CLASS;
    }
}
