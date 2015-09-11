<?php

namespace Trellis\Runtime\Attribute\Timestamp;

use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\ValueHolder\ValueHolder;
use DateTimeInterface;

/**
 * Default implementation used for datetime/timestamp value containment.
 */
class TimestampValueHolder extends ValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param mixed $other_value DateTimeInterface or acceptable datetime string
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        $value = $this->getValue();

        $equal = (
            (is_null($value) && is_null($other_value)) || // default value of timestamp attribute
            (is_null($value) && $other_value === '') || // empty strings are treated as null
            (
                ($value instanceof DateTimeInterface && $other_value instanceof DateTimeInterface) &&
                ($value == $other_value) && // no strict comparison as PHP then compares dates instead of instances
                ((int)$value->format('u') === (int)$other_value->format('u')) // compare the microseconds as well m(
            )
        );

        return $equal;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return string value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        if (!$this->getValue() instanceof DateTimeInterface) {
            return null;
        }

        return $this->getValue()->format(
            $this->getAttribute()->getOption(
                TimestampAttribute::OPTION_FORMAT_NATIVE,
                TimestampAttribute::FORMAT_ISO8601
            )
        );
    }

    public function acceptable($value)
    {
        $validation_result = $this->getAttribute()->getValidator()->validate($value);
        if ($validation_result->getSeverity() <= IncidentInterface::NOTICE) {
            return true;
        }

        return false;
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
        return 'string';
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
        return '\\' . DateTimeImmutable::CLASS;
    }
}
