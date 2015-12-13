<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Attribute\AttributeInterface;

class FloatRule extends Rule
{
    /**
     * Allow fraction separator (',' as in '1,200' === 1200)
     */
    const OPTION_ALLOW_THOUSAND_SEPARATOR = 'allow_thousand_separator';

    /**
     * precision when comparing two float values for equality. Falls back
     * to the php ini setting 'precision' (usually 14).
     */
    const OPTION_PRECISION_DIGITS = 'precision_digits';

    /**
     * Whether of not to accept infinite float values. Please note, that
     * the toNative representation of infinite values is a special string
     * that is known by the validation rule to set infinity as the internal
     * value on reconstitution. This string is most likely neither valid nor
     * acceptable in other representation formats that are created upon the
     * toNative representation (e.g. json_encode and reading that value via
     * javascript and through sorcery hope that it's a float).
     */
    const OPTION_ALLOW_INFINITY = 'allow_infinity';

    /**
     * Whether of not to accept NAN float values. Please note, that
     * the toNative representation of not-a-number values is a special string
     * that is known by the validation rule to set NAN as the internal
     * value on reconstitution. This string is most likely neither valid nor
     * acceptable in other representation formats that are created upon the
     * toNative representation (e.g. json_encode and reading that value via
     * javascript and through sorcery hope that it's a float).
     */
    const OPTION_ALLOW_NAN = 'allow_nan';

    const OPTION_MIN_VALUE = 'min_value';
    const OPTION_MAX_VALUE = 'max_value';

    protected function execute($value, EntityInterface $entity = null)
    {
        if ($value === '') {
            $value = $this->getOption(
                AttributeInterface::OPTION_NULL_VALUE,
                $this->getOption(
                    self::OPTION_MIN_VALUE,
                    $this->getOption(self::OPTION_MAX_VALUE, 0)
                )
            );

            $this->setSanitizedValue($value);

            return true;
        }

        $allow_thousand = $this->getOption(self::OPTION_ALLOW_THOUSAND_SEPARATOR, false);
        $allow_infinity = $this->getOption(self::OPTION_ALLOW_INFINITY, false);
        $allow_nan = $this->getOption(self::OPTION_ALLOW_NAN, false);

        $filter_flags = 0;
        if ($allow_thousand) {
            $filter_flags |= FILTER_FLAG_ALLOW_THOUSAND;
        }

        $sanitized = [];

        $float = filter_var($value, FILTER_VALIDATE_FLOAT, $filter_flags);

        if ($float === false || $value === true) {
            // float(NAN), float(INF) and float(-INF) are invalid floats according to filter_var
            if (!is_string($value) && !is_float($value)) {
                $this->throwError('non_float_value', [ 'value' => $value ]);
                return false;
            }

            // floats/strings 'NAN', 'INF' and '-INF' may be valid for us if attribute options say so
            // strings will be set with 'real' float values instead of staying strings
            $strval = strval($value);
            if ($strval === 'INF') {
                if (!$allow_infinity) {
                    $this->throwError('float_value_infinity', [ 'value' => $value ]);
                    return false;
                }
                $float = -log(0);
            } elseif ($strval === '-INF') {
                if (!$allow_infinity) {
                    $this->throwError('float_value_infinity', [ 'value' => $value ]);
                    return false;
                }
                $float = log(0);
            } elseif ($strval === 'NAN') {
                if (!$allow_nan) {
                    $this->throwError('float_value_nan', [ 'value' => $value ]);
                    return false;
                }
                $float = acos(1.01);
            } else {
                // not valid according to filter_var, not TRUE, not allowed NAN or INF value
                $this->throwError('non_float_value', [ 'value' => $value ]);
                return false;
            }
        }

        // check for NAN value (in case filter_var accepts those as valid in the future…)
        if (is_nan($float) && !$allow_nan) {
            $this->throwError('float_value_nan', [ 'value' => $float ]);
            return false;
        }

        // check for INFINITE value (in case filter_var accepts those as valid in the future…)
        if (is_infinite($float) && !$allow_infinity) {
            $this->throwError('float_value_infinity', [ 'value' => $float ]);
            return false;
        }

        // check minimum value
        if ($this->hasOption(self::OPTION_MIN_VALUE)) {
            $min = filter_var($this->getOption(self::OPTION_MIN_VALUE), FILTER_VALIDATE_FLOAT, $filter_flags);
            if ($min === false) {
                throw new InvalidConfigException('Minimum value specified is not interpretable as float.');
            }

            if ($float < $min) {
                $this->throwError(self::OPTION_MIN_VALUE, [
                    self::OPTION_MIN_VALUE => $min,
                    'value' => $float
                ]);
                return false;
            }
        }

        // check maximum value
        if ($this->hasOption(self::OPTION_MAX_VALUE)) {
            $max = filter_var($this->getOption(self::OPTION_MAX_VALUE), FILTER_VALIDATE_FLOAT, $filter_flags);
            if ($max === false) {
                throw new InvalidConfigException('Maximum value specified is not interpretable as float.');
            }

            if ($float > $max) {
                $this->throwError(self::OPTION_MAX_VALUE, [
                    self::OPTION_MAX_VALUE => $max,
                    'value' => $float
                ]);
                return false;
            }
        }

        $this->setSanitizedValue($float);

        return true;
    }
}
