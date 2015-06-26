<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\Type\BooleanRule;
use Trellis\Runtime\Validator\Rule\Type\FloatRule;
use Trellis\Runtime\Validator\Rule\Type\IntegerRule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Trellis\Runtime\Entity\EntityInterface;

/**
 * Only allows scalar input values while treating NULL as empty string.
 *
 * Delegates validation to other rules depending on the type:
 * - 'string' => TextRule
 * - 'float'/'double' => FloatRule
 * - 'integer' => IntegerRule
 * - 'boolean' => BooleanRule
 */
class ScalarRule extends Rule
{
    const OPTION_MAX_VALUE                  = 'max_value'; // when value_type is float or int
    const OPTION_MIN_VALUE                  = 'min_value'; // when value_type is float or int

    // text rule options
    const OPTION_ALLOW_CRLF                 = TextRule::OPTION_ALLOW_CRLF;
    const OPTION_ALLOW_TAB                  = TextRule::OPTION_ALLOW_TAB;
    const OPTION_MAX_LENGTH                 = TextRule::OPTION_MAX_LENGTH;
    const OPTION_MIN_LENGTH                 = TextRule::OPTION_MIN_LENGTH;
    const OPTION_NORMALIZE_NEWLINES         = TextRule::OPTION_NORMALIZE_NEWLINES;
    const OPTION_REJECT_INVALID_UTF8        = TextRule::OPTION_REJECT_INVALID_UTF8;
    const OPTION_STRIP_CONTROL_CHARACTERS   = TextRule::OPTION_STRIP_CONTROL_CHARACTERS;
    const OPTION_STRIP_DIRECTION_OVERRIDES  = TextRule::OPTION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_STRIP_INVALID_UTF8         = TextRule::OPTION_STRIP_INVALID_UTF8;
    const OPTION_STRIP_NULL_BYTES           = TextRule::OPTION_STRIP_NULL_BYTES;
    const OPTION_STRIP_ZERO_WIDTH_SPACE     = TextRule::OPTION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TRIM                       = TextRule::OPTION_TRIM;

    // integer rule options
    const OPTION_ALLOW_HEX                  = IntegerRule::OPTION_ALLOW_HEX;
    const OPTION_ALLOW_OCTAL                = IntegerRule::OPTION_ALLOW_OCTAL;
    const OPTION_MAX_INTEGER_VALUE          = 'max_integer_value'; // IntegerRule::OPTION_MAX_VALUE;
    const OPTION_MIN_INTEGER_VALUE          = 'min_integer_value'; // IntegerRule::OPTION_MIN_VALUE;

    // float rule options
    const OPTION_ALLOW_THOUSAND_SEPARATOR   = FloatRule::OPTION_ALLOW_THOUSAND_SEPARATOR;
    const OPTION_PRECISION_DIGITS           = FloatRule::OPTION_PRECISION_DIGITS;
    const OPTION_ALLOW_INFINITY             = FloatRule::OPTION_ALLOW_INFINITY;
    const OPTION_ALLOW_NAN                  = FloatRule::OPTION_ALLOW_NAN;
    const OPTION_MAX_FLOAT_VALUE            = 'max_float_value'; // FloatRule::OPTION_MAX_VALUE;
    const OPTION_MIN_FLOAT_VALUE            = 'min_float_value'; // FloatRule::OPTION_MIN_VALUE;

    protected function execute($value, EntityInterface $entity = null)
    {
        if (is_null($value)) {
            $value = '';
        }

        if (!is_scalar($value)) {
            $this->throwError('invalid_type', [ 'value' => $value ]);
            return false;
        }

        $rule = null;

        $value_type = gettype($value);
        switch ($value_type) {
            case 'integer':
                $rule = new IntegerRule('integer', $this->getIntegerOptions());
                break;
            case 'float':
            // in case gettype returns 'float' in future versions of php, fall trough.
            case 'double':
                $rule = new FloatRule('float', $this->getFloatOptions());
                break;
            case 'boolean':
                $rule = new BooleanRule('boolean', $this->getOptions());
                break;
            case 'string':
                $rule = new TextRule('text', $this->getOptions());
                break;
            default:
                $this->throwError('invalid_type', [ 'value' => $value ]);
                return false;
        }

        // validate value to be a valid string, integer, float or boolean
        if (!$rule->apply($value)) {
            $this->throwIncidentsAsErrors($rule);
            return false;
        }
        $value = $rule->getSanitizedValue();

        $this->setSanitizedValue($value);

        return true;
    }

    protected function getFloatOptions()
    {
        $float_options = $this->getOptions();

        if (array_key_exists(self::OPTION_MIN_FLOAT_VALUE, $float_options)) {
            $float_options[FloatRule::OPTION_MIN_VALUE] = $float_options[self::OPTION_MIN_FLOAT_VALUE];
        }

        if (array_key_exists(self::OPTION_MAX_FLOAT_VALUE, $float_options)) {
            $float_options[FloatRule::OPTION_MAX_VALUE] = $float_options[self::OPTION_MAX_FLOAT_VALUE];
        }

        return $float_options;
    }

    protected function getIntegerOptions()
    {
        $integer_options = $this->getOptions();

        if (array_key_exists(self::OPTION_MIN_INTEGER_VALUE, $integer_options)) {
            $integer_options[IntegerRule::OPTION_MIN_VALUE] = $integer_options[self::OPTION_MIN_INTEGER_VALUE];
        }
        if (array_key_exists(self::OPTION_MAX_INTEGER_VALUE, $integer_options)) {
            $integer_options[IntegerRule::OPTION_MAX_VALUE] = $integer_options[self::OPTION_MAX_INTEGER_VALUE];
        }

        return $integer_options;
    }
}
