<?php

namespace Trellis\Runtime\ValueHolder;

use Trellis\Runtime\Entity\EntityInterface;

/**
 * ValueHolders are not value objects. They hold an internal value of a certain
 * type, that will only be changed upon valid setValue calls. The internal
 * value can be scalars or other simple types, but it is suggested to use an
 * immutable value object to represent given information.
 *
 * Invalid values MUST not be accepted and the validation result should
 * indicate why the set operation failed. Only valid sanitized values MUST be
 * set. The validation rules that should be used to validate any given value
 * must also convert given types into the internally used value type.
 *
 * The valueholder should return a representation of the embedded value via the
 * toNative() method. The result of that call may be given to the setValue()
 * method again to reconstitute the internal value. This is useful to serialize
 * attribute value.
 *
 * The valueholder determines if any given value or valueholder equals itself.
 *
 * Interested parties can register to be notified of changes to the internal
 * value (that is, the exchange of one value against a new value) via events.
 */
interface ValueHolderInterface
{
    /**
     * Returns the value holder's embedded value. The type of the value can
     * be retrieved from the getValueType() method.
     *
     * @return mixed internal value or value object
     */
    public function getValue();

    /**
     * Sets the value holder's value. This method MUST accept values of the
     * native type and SHOULD accept other formats including the internally
     * used value type.
     *
     * The given value MUST be validated and the result of the validation
     * will be returned. When validation fails the value MUST NOT be set as
     * the internal value.
     *
     * @param mixed $value representation of value to set
     * @param EntityInterface $entity
     *
     * @return \Trellis\Runtime\Validator\Result\ResultInterface validation result
     */
    public function setValue($value, EntityInterface $entity = null);

    /**
     * Tells if a value holder has no value set.
     *
     * @return boolean true if the internal value if considered to be nil
     */
    public function isNull();

    /**
     * Tells whether the valueholder's value is considered to be the same as
     * the default value defined on the attribute.
     *
     * @return boolean true if the internal value is considered to be default
     */
    public function isDefault();

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
    public function getNativeType();

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative();

    /**
     * Returns the type of the internal value of the valueholder. This can
     * be anything from 'string', 'array' or 'int' to a fully qualified class
     * name of the value object or PHP object used for storage internally.
     *
     * The returned type is the one returned by getValue() method calls.
     *
     * @return string type or FQCN of the internal value
     */
    public function getValueType();

    /**
     * Tells whether the given value is considered equal to the internal value.
     * Please note, that this method MUST handle the native type as well.
     *
     * @param mixed $other_value value to compare
     *
     * @return boolean true if the other value is considered equal
     */
    public function sameValueAs($other_value);

    /**
     * Tells whether the given valueholder is considered being equal to the
     * current instance. That is, class name and value are considered the same
     * whileas the actual attribute and entity may be different.
     *
     * @param ValueHolderInterface $other_value_holder
     *
     * @return boolean true if the given valueholder is considered equal
     */
    public function isEqualTo(ValueHolderInterface $other_value_holder);

    /**
     * Registers a given listener as a recipient of value changed events.
     *
     * @param ValueChangedListenerInterface $listener
     */
    public function addValueChangedListener(ValueChangedListenerInterface $listener);

    /**
     * Removes a given listener as from the list of value changed listeners.
     *
     * @param ValueChangedListenerInterface $listener
     */
    public function removedValueChangedListener(ValueChangedListenerInterface $listener);
}
