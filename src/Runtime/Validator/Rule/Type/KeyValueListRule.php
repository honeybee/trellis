<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Attribute\Float\FloatAttribute;
use Trellis\Runtime\Attribute\Integer\IntegerAttribute;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\Type\BooleanRule;
use Trellis\Runtime\Validator\Rule\Type\FloatRule;
use Trellis\Runtime\Validator\Rule\Type\IntegerRule;
use Trellis\Runtime\Validator\Rule\Type\ScalarRule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Trellis\Runtime\Entity\EntityInterface;

class KeyValueListRule extends Rule
{
    const OPTION_ALLOWED_KEYS               = 'allowed_keys';
    const OPTION_ALLOWED_VALUES             = 'allowed_values';
    const OPTION_ALLOWED_PAIRS              = 'allowed_pairs';

    /**
     * Option to define that values must be of a certain scalar type.
     */
    const OPTION_VALUE_TYPE                 = 'value_type';

    const VALUE_TYPE_BOOLEAN                = 'boolean';
    const VALUE_TYPE_INTEGER                = 'integer';
    const VALUE_TYPE_FLOAT                  = 'float';
    const VALUE_TYPE_SCALAR                 = 'scalar'; // any of integer, float, boolean or string
    const VALUE_TYPE_TEXT                   = 'text';

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
        if (!is_array($value)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        if (!empty($value) && !$this->isAssoc($value)) {
            $this->throwError('non_assoc_array', [], IncidentInterface::CRITICAL);
            return false;
        }

        $allowed_values = [];
        if ($this->hasOption(self::OPTION_ALLOWED_VALUES)) {
            $allowed_values = $this->getAllowedValues();
        }

        $allowed_keys = [];
        if ($this->hasOption(self::OPTION_ALLOWED_KEYS)) {
            $allowed_keys = $this->getAllowedKeys();
        }

        $allowed_pairs = [];
        if ($this->hasOption(self::OPTION_ALLOWED_PAIRS)) {
            $allowed_pairs = $this->getAllowedPairs();
        }

        $sanitized = [];

        $options = $this->getOptions();

        $value_type = $this->getOption(self::OPTION_VALUE_TYPE, self::VALUE_TYPE_SCALAR);

        $rule = null;
        switch ($value_type) {
            case self::VALUE_TYPE_INTEGER:
                $rule = new IntegerRule('integer', $this->getIntegerOptions());
                break;
            case self::VALUE_TYPE_FLOAT:
                $rule = new FloatRule('float', $this->getFloatOptions());
                break;
            case self::VALUE_TYPE_BOOLEAN:
                $rule = new BooleanRule('boolean', $this->getOptions());
                break;
            case self::VALUE_TYPE_TEXT:
                $rule = new TextRule('text', $this->getOptions());
                break;
            case self::VALUE_TYPE_SCALAR:
            default:
                $rule = new ScalarRule('scalar', $this->getOptions());
                break;
        }

        foreach ($value as $key => $val) {
            if (is_numeric($key)) {
                $this->throwError('numeric_key', [], IncidentInterface::NOTICE);
            }

            $key = trim($key);
            if (!strlen($key)) {
                $this->throwError('empty_key', [], IncidentInterface::CRITICAL);
                return false;
            }

            // check for allowed keys
            if ($this->hasOption(self::OPTION_ALLOWED_KEYS)) {
                if (!in_array($key, $allowed_keys, true)) {
                    $this->throwError(
                        self::OPTION_ALLOWED_KEYS,
                        [
                            self::OPTION_ALLOWED_KEYS => $allowed_keys,
                            'key' => $key
                        ]
                    );
                    return false;
                }
            }

            if (is_null($val)) {
                $val = '';
            }

            if (!is_scalar($val)) {
                $this->throwError('non_scalar_value', [ 'key' => $key ], IncidentInterface::CRITICAL);
                return false;
            }

            // we accept simple scalar types to be casted to strings
            if ($value_type === self::VALUE_TYPE_TEXT) {
                $val = (string)$val;
            }

            // validate value to be string, integer, float or boolean
            if (!$rule->apply($val)) {
                $this->throwIncidentsAsErrors($rule);
                return false;
            }
            $val = $rule->getSanitizedValue();

            // check for allowed values
            if ($this->hasOption(self::OPTION_ALLOWED_VALUES)) {
                // use FloatAttribute if equal value comparison of float values if important
                if (!in_array($val, $allowed_values, true)) {
                    $this->throwError(
                        self::OPTION_ALLOWED_VALUES,
                        [
                            self::OPTION_ALLOWED_VALUES => $allowed_values,
                            'value' => $val
                        ]
                    );
                    return false;
                }
            }

            // check for allowed key => values pairs
            if ($this->hasOption(self::OPTION_ALLOWED_PAIRS)) {
                // use FloatAttribute if equal value comparison of float values is important (w/ precision)
                if (!(array_key_exists($key, $allowed_pairs) && $allowed_pairs[$key] === $val)) {
                    $this->throwError(
                        self::OPTION_ALLOWED_PAIRS,
                        [
                            self::OPTION_ALLOWED_PAIRS => $allowed_pairs,
                            'key' => $key,
                            'value' => $val
                        ]
                    );
                    return false;
                }
            }

            $sanitized[$key] = $val;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }

    /**
     * @return bool true if argument is an associative array. False otherwise.
     */
    protected function isAssoc(array $array)
    {
        foreach (array_keys($array) as $key => $value) {
            if ($key !== $value) {
                return true;
            }
        }

        return false;
    }

    protected function getAllowedKeys()
    {
        $allowed_keys = [];

        $configured_allowed_keys = $this->getOption(self::OPTION_ALLOWED_KEYS, []);
        if (!is_array($configured_allowed_keys)) {
            throw new InvalidConfigException('Configured allowed_keys must be an array of permitted key names.');
        }

        foreach ($configured_allowed_keys as $key) {
            $allowed_keys[] = (string)$key;
        }

        return $allowed_keys;
    }

    protected function getAllowedValues()
    {
        $configured_allowed_values = $this->getOption(self::OPTION_ALLOWED_VALUES, []);
        if (!is_array($configured_allowed_values)) {
            throw new InvalidConfigException(
                'Configured allowed_values must be an array of permitted values.'
            );
        }

        return $this->castArray($configured_allowed_values);
    }

    protected function getAllowedPairs()
    {
        $configured_allowed_pairs = $this->getOption(self::OPTION_ALLOWED_PAIRS, []);
        if (!is_array($configured_allowed_pairs)) {
            throw new InvalidConfigException(
                'Configured allowed_pairs must be an array of permitted key => value pairs.'
            );
        }

        return $this->castArray($configured_allowed_pairs);
    }

    protected function castArray($array)
    {
        $value_type = $this->getOption(self::OPTION_VALUE_TYPE, self::VALUE_TYPE_SCALAR);

        $casted = [];

        foreach ($array as $key => $raw) {
            switch ($value_type) {
                case self::VALUE_TYPE_INTEGER:
                    $casted_value = filter_var($raw, FILTER_VALIDATE_INT, $this->getIntegerFilterFlags());
                    if ($casted_value === false || $raw === true) {
                        throw new InvalidConfigException('Allowed integer values must be interpretable as integers.');
                    }
                    break;

                case self::VALUE_TYPE_FLOAT:
                    $casted_value = filter_var($raw, FILTER_VALIDATE_FLOAT, $this->getFloatFilterFlags());
                    if ($casted_value === false || $raw === true) {
                        throw new InvalidConfigException(
                            'Allowed float values must be interpretable as floats. NAN or +-INF values are not ' .
                            'supported. The thousand separator (,) may be configured via attribute options.'
                        );
                    }
                    break;

                case self::VALUE_TYPE_TEXT:
                    $casted_value = (string)$raw;
                    break;

                case self::VALUE_TYPE_BOOLEAN:
                    $casted_value = $this->toBoolean($raw);
                    break;

                case self::VALUE_TYPE_SCALAR:
                default:
                    $casted_value = $raw;
                    break;
            }

            $casted[(string)$key] = $casted_value;
        }

        return $casted;
    }

    protected function getIntegerFilterFlags()
    {
        $allow_hex = $this->getOption(IntegerAttribute::OPTION_ALLOW_HEX, false);
        $allow_octal = $this->getOption(IntegerAttribute::OPTION_ALLOW_OCTAL, false);

        $filter_flags = 0;
        if ($allow_hex) {
            $filter_flags |= FILTER_FLAG_ALLOW_HEX;
        }
        if ($allow_octal) {
            $filter_flags |= FILTER_FLAG_ALLOW_OCTAL;
        }

        return $filter_flags;
    }

    protected function getFloatFilterFlags()
    {
        $allow_thousand = $this->getOption(FloatAttribute::OPTION_ALLOW_THOUSAND_SEPARATOR, false);

        $filter_flags = 0;
        if ($allow_thousand) {
            $filter_flags |= FILTER_FLAG_ALLOW_THOUSAND;
        }

        return $filter_flags;
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
