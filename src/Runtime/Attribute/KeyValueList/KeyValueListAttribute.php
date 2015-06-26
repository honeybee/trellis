<?php

namespace Trellis\Runtime\Attribute\KeyValueList;

use Trellis\Runtime\Attribute\ListAttribute;
use Trellis\Runtime\Validator\Rule\Type\KeyValueListRule;

/**
 * A list of key => value pairs where:
 *
 * - key is a string
 * - value is a string, integer, boolean or float
 */
class KeyValueListAttribute extends ListAttribute
{
    const OPTION_ALLOWED_KEYS               = KeyValueListRule::OPTION_ALLOWED_KEYS;
    const OPTION_ALLOWED_VALUES             = KeyValueListRule::OPTION_ALLOWED_VALUES;
    const OPTION_ALLOWED_PAIRS              = KeyValueListRule::OPTION_ALLOWED_PAIRS;

    /**
     * Option to define that values must be of a certain scalar type.
     */
    const OPTION_VALUE_TYPE                 = KeyValueListRule::OPTION_VALUE_TYPE;

    const VALUE_TYPE_BOOLEAN                = KeyValueListRule::VALUE_TYPE_BOOLEAN;
    const VALUE_TYPE_INTEGER                = KeyValueListRule::VALUE_TYPE_INTEGER;
    const VALUE_TYPE_FLOAT                  = KeyValueListRule::VALUE_TYPE_FLOAT;
    const VALUE_TYPE_SCALAR                 = KeyValueListRule::VALUE_TYPE_SCALAR;
    const VALUE_TYPE_TEXT                   = KeyValueListRule::VALUE_TYPE_TEXT;

    const OPTION_MAX_VALUE                  = KeyValueListRule::OPTION_MAX_VALUE; // when value_type is float or int
    const OPTION_MIN_VALUE                  = KeyValueListRule::OPTION_MIN_VALUE; // when value_type is float or int

    // text rule options
    const OPTION_ALLOW_CRLF                 = KeyValueListRule::OPTION_ALLOW_CRLF;
    const OPTION_ALLOW_TAB                  = KeyValueListRule::OPTION_ALLOW_TAB;
    const OPTION_MAX_LENGTH                 = KeyValueListRule::OPTION_MAX_LENGTH;
    const OPTION_MIN_LENGTH                 = KeyValueListRule::OPTION_MIN_LENGTH;
    const OPTION_NORMALIZE_NEWLINES         = KeyValueListRule::OPTION_NORMALIZE_NEWLINES;
    const OPTION_REJECT_INVALID_UTF8        = KeyValueListRule::OPTION_REJECT_INVALID_UTF8;
    const OPTION_STRIP_CONTROL_CHARACTERS   = KeyValueListRule::OPTION_STRIP_CONTROL_CHARACTERS;
    const OPTION_STRIP_DIRECTION_OVERRIDES  = KeyValueListRule::OPTION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_STRIP_INVALID_UTF8         = KeyValueListRule::OPTION_STRIP_INVALID_UTF8;
    const OPTION_STRIP_NULL_BYTES           = KeyValueListRule::OPTION_STRIP_NULL_BYTES;
    const OPTION_STRIP_ZERO_WIDTH_SPACE     = KeyValueListRule::OPTION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TRIM                       = KeyValueListRule::OPTION_TRIM;

    // integer rule options
    const OPTION_ALLOW_HEX                  = KeyValueListRule::OPTION_ALLOW_HEX;
    const OPTION_ALLOW_OCTAL                = KeyValueListRule::OPTION_ALLOW_OCTAL;
    const OPTION_MAX_INTEGER_VALUE          = KeyValueListRule::OPTION_MAX_INTEGER_VALUE;
    const OPTION_MIN_INTEGER_VALUE          = KeyValueListRule::OPTION_MIN_INTEGER_VALUE;

    // float rule options
    const OPTION_ALLOW_THOUSAND_SEPARATOR   = KeyValueListRule::OPTION_ALLOW_THOUSAND_SEPARATOR;
    const OPTION_PRECISION_DIGITS           = KeyValueListRule::OPTION_PRECISION_DIGITS;
    const OPTION_ALLOW_INFINITY             = KeyValueListRule::OPTION_ALLOW_INFINITY;
    const OPTION_ALLOW_NAN                  = KeyValueListRule::OPTION_ALLOW_NAN;
    const OPTION_MAX_FLOAT_VALUE            = KeyValueListRule::OPTION_MAX_FLOAT_VALUE;
    const OPTION_MIN_FLOAT_VALUE            = KeyValueListRule::OPTION_MIN_FLOAT_VALUE;

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new KeyValueListRule('valid-key-value-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
