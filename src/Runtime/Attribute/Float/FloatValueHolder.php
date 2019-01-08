<?php

namespace Trellis\Runtime\Attribute\Float;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Common\Error\RuntimeException;
use Trellis\Runtime\ValueHolder\ValueHolder;

/**
 * Default implementation used for float value containment.
 */
class FloatValueHolder extends ValueHolder
{
    const FLOAT_MIN = 1.17549435e-38; // took this from java :D

    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param float $other_value number value to compare
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        if (!is_float($other_value)) {
            return false;
        }

        return $this->almostEqual($this->getValue(), $other_value, $this->getEpsilon());
        //return (abs($this->getValue() - $other_value) < $this->getEpsilon());
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     * BEWARE that allowing INF or NAN values via attribute options leads to
     * this method returning INF/NAN floats as string.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        $allow_infinity = $this->getAttribute()->getOption(FloatAttribute::OPTION_ALLOW_INFINITY, false);
        if (is_infinite($this->getValue())) {
            return (string)$this->getValue();
        }

        $allow_nan = $this->getAttribute()->getOption(FloatAttribute::OPTION_ALLOW_NAN, false);
        if (is_nan($this->getValue())) {
            return (string)$this->getValue();
        }

        return $this->getValue();
    }

    /**
     * Returns the type of the value that is returned for the toNative() call.
     * This is used for typehints in code generation and might be used in other
     * layers (e.g. web form submissions) to handle things differently.
     * BEWARE that allowing INF or NAN values via attribute options leads to
     * the toNative() method returning INF/NAN floats as type string.
     *
     * @return string return type of the toNative() method
     */
    public function getNativeType()
    {
        return 'float';
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
        return 'float';
    }

    /**
     * Compare two float values for equality.
     *
     * @see http://floating-point-gui.de/errors/comparison/
     *
     * @param float $a
     * @param float $b
     * @param float $epsilon delta when comparing unequal values
     *
     * @return boolean true if float values are considered to be of equal value
     */
    public static function almostEqual($a, $b, $epsilon = 0.0000000001)
    {
        $diff = abs($a - $b);

        if ($a === $b) {
            // just compare values, handles e.g. INF
            return true;
        } elseif ($a === 0.0 || $b === 0.0 || $diff < self::FLOAT_MIN) {
            // a or b is zero or both are extremely close to it
            // relative error is less meaningful here
            return $diff < ($epsilon * self::FLOAT_MIN);
        } else {
            // use relative error
            $abs_a = abs($a);
            $abs_b = abs($b);
            return $diff / ($abs_a + $abs_b) < $epsilon;
        }
    }


    /**
     * @return float value that can be used as delta/epsilon for float value equality comparisons
     */
    protected function getEpsilon()
    {
        $precision_digits = $this->getPrecisionDigits();

        $epsilon = filter_var("1e-" . abs($precision_digits), FILTER_VALIDATE_FLOAT);
        if ($epsilon === false || $precision_digits === true) {
            throw new InvalidConfigException(
                "Could not interprete float precision digits correctly. Please specify a positive integer (e.g. 16)."
            );
        }

        return $epsilon;
    }

    protected function getPrecisionDigits()
    {
        $precision_digits_value = $this->getAttribute()->getOption(FloatAttribute::OPTION_PRECISION_DIGITS, 14);
        $precision_digits = filter_var($precision_digits_value, FILTER_VALIDATE_INT);
        if ($precision_digits === false || $precision_digits_value === true) {
            trigger_error(
                "The configured number of digits for float precision is not interpretable as integer. " .
                "Using fallback of 14 digits.",
                E_USER_WARNING
            );
            $precision_digits = 14;
        }

        return $precision_digits;
    }
}
