<?php

namespace Trellis\Runtime\Attribute\Asset;

use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\Type\FloatRule;
use Trellis\Runtime\Validator\Rule\Type\IntegerRule;
use Trellis\Runtime\Validator\Rule\Type\KeyValueListRule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Trellis\Runtime\Validator\Rule\Type\UrlRule;
use Trellis\Runtime\Entity\EntityInterface;
use Exception;

class AssetRule extends Rule
{
    // restrict meta_data to certain keys or values or key-value pairs
    const OPTION_META_DATA_ALLOWED_KEYS                     = 'meta_data_allowed_keys';
    const OPTION_META_DATA_ALLOWED_VALUES                   = 'meta_data_allowed_values';
    const OPTION_META_DATA_ALLOWED_PAIRS                    = 'meta_data_allowed_pairs';

    /**
     * Option to define that meta_data values must be of a certain scalar type.
     */
    const OPTION_META_DATA_VALUE_TYPE                       = 'meta_data_value_type';

    const META_DATA_VALUE_TYPE_BOOLEAN                      = 'boolean';
    const META_DATA_VALUE_TYPE_INTEGER                      = 'integer';
    const META_DATA_VALUE_TYPE_FLOAT                        = 'float';
    const META_DATA_VALUE_TYPE_SCALAR                       = 'scalar'; // default; any of int, float, bool or string
    const META_DATA_VALUE_TYPE_TEXT                         = 'text';

    const OPTION_META_DATA_MAX_VALUE                        = 'meta_data_max_value'; // when type is float or integer
    const OPTION_META_DATA_MIN_VALUE                        = 'meta_data_min_value'; // when type is float or integer

    // text rule options for meta_data
    const OPTION_META_DATA_ALLOW_CRLF                       = 'meta_data_allow_crlf';
    const OPTION_META_DATA_ALLOW_TAB                        = 'meta_data_allow_tab';
    const OPTION_META_DATA_MAX_LENGTH                       = 'meta_data_max_length';
    const OPTION_META_DATA_MIN_LENGTH                       = 'meta_data_min_length';
    const OPTION_META_DATA_NORMALIZE_NEWLINES               = 'meta_data_normalize_newlines';
    const OPTION_META_DATA_REJECT_INVALID_UTF8              = 'meta_data_reject_invalid_utf8';
    const OPTION_META_DATA_STRIP_CONTROL_CHARACTERS         = 'meta_data_strip_control_characters';
    const OPTION_META_DATA_STRIP_DIRECTION_OVERRIDES        = 'meta_data_strip_direction_overrides';
    const OPTION_META_DATA_STRIP_INVALID_UTF8               = 'meta_data_strip_invalid_utf8';
    const OPTION_META_DATA_STRIP_NULL_BYTES                 = 'meta_data_strip_null_bytes';
    const OPTION_META_DATA_STRIP_ZERO_WIDTH_SPACE           = 'meta_data_strip_zero_width_space';
    const OPTION_META_DATA_TRIM                             = 'meta_data_trim';

    // integer rule options for meta_data
    const OPTION_META_DATA_ALLOW_HEX                        = 'meta_data_allow_hex';
    const OPTION_META_DATA_ALLOW_OCTAL                      = 'meta_data_allow_octal';
    const OPTION_META_DATA_MAX_INTEGER_VALUE                = 'meta_data_max_integer_value';
    const OPTION_META_DATA_MIN_INTEGER_VALUE                = 'meta_data_min_integer_value';

    // float rule options for meta_data
    const OPTION_META_DATA_ALLOW_THOUSAND_SEPARATOR         = 'meta_data_allow_thousand_separator';
    const OPTION_META_DATA_PRECISION_DIGITS                 = 'meta_data_precision_digits';
    const OPTION_META_DATA_ALLOW_INFINITY                   = 'meta_data_allow_infinity';
    const OPTION_META_DATA_ALLOW_NAN                        = 'meta_data_allow_nan';
    const OPTION_META_DATA_MAX_FLOAT_VALUE                  = 'meta_data_max_float_value';
    const OPTION_META_DATA_MIN_FLOAT_VALUE                  = 'meta_data_min_float_value';

    protected $validations = [
        Asset::PROPERTY_LOCATION        => TextRule::CLASS,
        Asset::PROPERTY_TITLE           => TextRule::CLASS,
        Asset::PROPERTY_CAPTION         => TextRule::CLASS,
        Asset::PROPERTY_COPYRIGHT       => TextRule::CLASS,
        Asset::PROPERTY_COPYRIGHT_URL   => UrlRule::CLASS,
        Asset::PROPERTY_SOURCE          => TextRule::CLASS
    ];

    protected $meta_data_options = [
        self::OPTION_META_DATA_ALLOWED_KEYS,
        self::OPTION_META_DATA_ALLOWED_VALUES,
        self::OPTION_META_DATA_ALLOWED_PAIRS,
        self::OPTION_META_DATA_VALUE_TYPE,
        self::OPTION_META_DATA_MAX_VALUE,
        self::OPTION_META_DATA_MIN_VALUE,

        self::OPTION_META_DATA_ALLOW_CRLF,
        self::OPTION_META_DATA_ALLOW_TAB,
        self::OPTION_META_DATA_MAX_LENGTH,
        self::OPTION_META_DATA_MIN_LENGTH,
        self::OPTION_META_DATA_NORMALIZE_NEWLINES,
        self::OPTION_META_DATA_REJECT_INVALID_UTF8,
        self::OPTION_META_DATA_STRIP_CONTROL_CHARACTERS,
        self::OPTION_META_DATA_STRIP_DIRECTION_OVERRIDES,
        self::OPTION_META_DATA_STRIP_INVALID_UTF8,
        self::OPTION_META_DATA_STRIP_NULL_BYTES,
        self::OPTION_META_DATA_STRIP_ZERO_WIDTH_SPACE,
        self::OPTION_META_DATA_TRIM,

        self::OPTION_META_DATA_ALLOW_HEX,
        self::OPTION_META_DATA_ALLOW_OCTAL,
        self::OPTION_META_DATA_MAX_INTEGER_VALUE,
        self::OPTION_META_DATA_MIN_INTEGER_VALUE,

        self::OPTION_META_DATA_ALLOW_THOUSAND_SEPARATOR,
        self::OPTION_META_DATA_PRECISION_DIGITS,
        self::OPTION_META_DATA_ALLOW_INFINITY,
        self::OPTION_META_DATA_ALLOW_NAN,
        self::OPTION_META_DATA_MAX_FLOAT_VALUE,
        self::OPTION_META_DATA_MIN_FLOAT_VALUE
    ];
    protected function execute($value, EntityInterface $entity = null)
    {
        try {
            if (is_array($value)) {
                if (!empty($value) && !$this->isAssoc($value)) {
                    $this->throwError('non_assoc_array', [ 'value' => $value ], IncidentInterface::CRITICAL);
                    return false;
                }
                $asset = Asset::createFromArray($value);
            } elseif ($value instanceof Asset) {
                $asset = Asset::createFromArray($value->toNative());
            } else {
                $this->throwError('invalid_type', [ 'value' => $value ], IncidentInterface::CRITICAL);
                return false;
            }

            $incoming_data = $asset->toNative();

            $data = [];

            foreach ($this->validations as $property_name => $implementor) {
                $rule_options = $this->getSupportedOptionsFor($implementor, $property_name);
                $rule = new $implementor('valid-' . $property_name, $rule_options);

                if (!$rule->apply($incoming_data[$property_name])) {
                    $this->throwIncidentsAsErrors($rule, $property_name);
                    return false;
                }
                $data[$property_name] = $rule->getSanitizedValue();
            }

            // meta data accepts scalar values
            $rule = new KeyValueListRule('valid-meta-data', $this->getMetaDataOptions());
            if (!$rule->apply($incoming_data[Asset::PROPERTY_META_DATA])) {
                $this->throwIncidentsAsErrors($rule, Asset::PROPERTY_META_DATA);
                return false;
            }
            $data[Asset::PROPERTY_META_DATA] = $rule->getSanitizedValue();

            // set the sanitized new data
            $this->setSanitizedValue(Asset::createFromArray($data));
        } catch (Exception $e) {
            // pretty catch all, but there may be Assert and BadValueExceptions depending on usage / later changes
            $this->throwError(
                'invalid_data',
                [
                    'error' => $e->getMessage()
                ],
                IncidentInterface::CRITICAL
            );
            return false;
        }

        return true;
    }

    protected function getMetaDataOptions()
    {
        $options = $this->getOptions();

        $value_type = self::META_DATA_VALUE_TYPE_SCALAR;
        if (array_key_exists(self::OPTION_META_DATA_VALUE_TYPE, $options)) {
            $value_type = $options[self::OPTION_META_DATA_VALUE_TYPE];
        }

        // when a specific value type is forced use the specific min/max option value instead of the generic one
        if ($value_type === self::META_DATA_VALUE_TYPE_FLOAT) {
            if (array_key_exists(self::OPTION_META_DATA_MIN_FLOAT_VALUE, $options)) {
                $options[FloatRule::OPTION_MIN_VALUE] = $options[self::OPTION_META_DATA_MIN_FLOAT_VALUE];
            }

            if (array_key_exists(self::OPTION_META_DATA_MAX_FLOAT_VALUE, $options)) {
                $options[FloatRule::OPTION_MAX_VALUE] = $options[self::OPTION_META_DATA_MAX_FLOAT_VALUE];
            }
        } elseif ($value_type === self::META_DATA_VALUE_TYPE_INTEGER) {
            if (array_key_exists(self::OPTION_META_DATA_MIN_INTEGER_VALUE, $options)) {
                $options[IntegerRule::OPTION_MIN_VALUE] = $options[self::OPTION_META_DATA_MIN_INTEGER_VALUE];
            }

            if (array_key_exists(self::OPTION_META_DATA_MAX_INTEGER_VALUE, $options)) {
                $options[IntegerRule::OPTION_MAX_VALUE] = $options[self::OPTION_META_DATA_MAX_INTEGER_VALUE];
            }
        }

        $kvl_options = [];

        // map all meta_data options to normal KeyValueListRule supported options
        foreach ($this->meta_data_options as $name) {
            if (array_key_exists($name, $options)) {
                $opt_name = str_replace('meta_data_', '', $name);
                $kvl_options[$opt_name] = $options[$name];
            }
        }

        return $kvl_options;
    }
}
