<?php

namespace Trellis\Runtime\Attribute\Image;

use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\Type\FloatRule;
use Trellis\Runtime\Validator\Rule\Type\IntegerRule;
use Trellis\Runtime\Validator\Rule\Type\KeyValueListRule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Trellis\Runtime\Validator\Rule\Type\UrlRule;
use Exception;
use Trellis\Runtime\Entity\EntityInterface;

class ImageRule extends Rule
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

    // text rule options for location property
    const OPTION_LOCATION_ALLOW_CRLF                        = 'location_allow_crlf';
    const OPTION_LOCATION_ALLOW_TAB                         = 'location_allow_tab';
    const OPTION_LOCATION_MAX_LENGTH                        = 'location_max_length';
    const OPTION_LOCATION_MIN_LENGTH                        = 'location_min_length';
    const OPTION_LOCATION_NORMALIZE_NEWLINES                = 'location_normalize_newlines';
    const OPTION_LOCATION_REJECT_INVALID_UTF8               = 'location_reject_invalid_utf8';
    const OPTION_LOCATION_STRIP_CONTROL_CHARACTERS          = 'location_strip_control_characters';
    const OPTION_LOCATION_STRIP_DIRECTION_OVERRIDES         = 'location_strip_direction_overrides';
    const OPTION_LOCATION_STRIP_INVALID_UTF8                = 'location_strip_invalid_utf8';
    const OPTION_LOCATION_STRIP_NULL_BYTES                  = 'location_strip_null_bytes';
    const OPTION_LOCATION_STRIP_ZERO_WIDTH_SPACE            = 'location_strip_zero_width_space';
    const OPTION_LOCATION_TRIM                              = 'location_trim';

    // text rule options for title property
    const OPTION_TITLE_ALLOW_CRLF                           = 'title_allow_crlf';
    const OPTION_TITLE_ALLOW_TAB                            = 'title_allow_tab';
    const OPTION_TITLE_MAX_LENGTH                           = 'title_max_length';
    const OPTION_TITLE_MIN_LENGTH                           = 'title_min_length';
    const OPTION_TITLE_NORMALIZE_NEWLINES                   = 'title_normalize_newlines';
    const OPTION_TITLE_REJECT_INVALID_UTF8                  = 'title_reject_invalid_utf8';
    const OPTION_TITLE_STRIP_CONTROL_CHARACTERS             = 'title_strip_control_characters';
    const OPTION_TITLE_STRIP_DIRECTION_OVERRIDES            = 'title_strip_direction_overrides';
    const OPTION_TITLE_STRIP_INVALID_UTF8                   = 'title_strip_invalid_utf8';
    const OPTION_TITLE_STRIP_NULL_BYTES                     = 'title_strip_null_bytes';
    const OPTION_TITLE_STRIP_ZERO_WIDTH_SPACE               = 'title_strip_zero_width_space';
    const OPTION_TITLE_TRIM                                 = 'title_trim';

    // text rule options for caption property
    const OPTION_CAPTION_ALLOW_CRLF                         = 'caption_allow_crlf';
    const OPTION_CAPTION_ALLOW_TAB                          = 'caption_allow_tab';
    const OPTION_CAPTION_MAX_LENGTH                         = 'caption_max_length';
    const OPTION_CAPTION_MIN_LENGTH                         = 'caption_min_length';
    const OPTION_CAPTION_NORMALIZE_NEWLINES                 = 'caption_normalize_newlines';
    const OPTION_CAPTION_REJECT_INVALID_UTF8                = 'caption_reject_invalid_utf8';
    const OPTION_CAPTION_STRIP_CONTROL_CHARACTERS           = 'caption_strip_control_characters';
    const OPTION_CAPTION_STRIP_DIRECTION_OVERRIDES          = 'caption_strip_direction_overrides';
    const OPTION_CAPTION_STRIP_INVALID_UTF8                 = 'caption_strip_invalid_utf8';
    const OPTION_CAPTION_STRIP_NULL_BYTES                   = 'caption_strip_null_bytes';
    const OPTION_CAPTION_STRIP_ZERO_WIDTH_SPACE             = 'caption_strip_zero_width_space';
    const OPTION_CAPTION_TRIM                               = 'caption_trim';

    // text rule options for copyright property
    const OPTION_COPYRIGHT_ALLOW_CRLF                       = 'copyright_allow_crlf';
    const OPTION_COPYRIGHT_ALLOW_TAB                        = 'copyright_allow_tab';
    const OPTION_COPYRIGHT_MAX_LENGTH                       = 'copyright_max_length';
    const OPTION_COPYRIGHT_MIN_LENGTH                       = 'copyright_min_length';
    const OPTION_COPYRIGHT_NORMALIZE_NEWLINES               = 'copyright_normalize_newlines';
    const OPTION_COPYRIGHT_REJECT_INVALID_UTF8              = 'copyright_reject_invalid_utf8';
    const OPTION_COPYRIGHT_STRIP_CONTROL_CHARACTERS         = 'copyright_strip_control_characters';
    const OPTION_COPYRIGHT_STRIP_DIRECTION_OVERRIDES        = 'copyright_strip_direction_overrides';
    const OPTION_COPYRIGHT_STRIP_INVALID_UTF8               = 'copyright_strip_invalid_utf8';
    const OPTION_COPYRIGHT_STRIP_NULL_BYTES                 = 'copyright_strip_null_bytes';
    const OPTION_COPYRIGHT_STRIP_ZERO_WIDTH_SPACE           = 'copyright_strip_zero_width_space';
    const OPTION_COPYRIGHT_TRIM                             = 'copyright_trim';

    // text rule options for source property
    const OPTION_SOURCE_ALLOW_CRLF                          = 'source_allow_crlf';
    const OPTION_SOURCE_ALLOW_TAB                           = 'source_allow_tab';
    const OPTION_SOURCE_MAX_LENGTH                          = 'source_max_length';
    const OPTION_SOURCE_MIN_LENGTH                          = 'source_min_length';
    const OPTION_SOURCE_NORMALIZE_NEWLINES                  = 'source_normalize_newlines';
    const OPTION_SOURCE_REJECT_INVALID_UTF8                 = 'source_reject_invalid_utf8';
    const OPTION_SOURCE_STRIP_CONTROL_CHARACTERS            = 'source_strip_control_characters';
    const OPTION_SOURCE_STRIP_DIRECTION_OVERRIDES           = 'source_strip_direction_overrides';
    const OPTION_SOURCE_STRIP_INVALID_UTF8                  = 'source_strip_invalid_utf8';
    const OPTION_SOURCE_STRIP_NULL_BYTES                    = 'source_strip_null_bytes';
    const OPTION_SOURCE_STRIP_ZERO_WIDTH_SPACE              = 'source_strip_zero_width_space';
    const OPTION_SOURCE_TRIM                                = 'source_trim';

    // text rule options
    const OPTION_ALLOW_CRLF                = 'allow_crlf';
    const OPTION_ALLOW_TAB                 = 'allow_tab';
    const OPTION_MAX_LENGTH                = 'max_length';
    const OPTION_MIN_LENGTH                = 'min_length';
    const OPTION_NORMALIZE_NEWLINES        = 'normalize_newlines';
    const OPTION_REJECT_INVALID_UTF8       = 'reject_invalid_utf8';
    const OPTION_STRIP_CONTROL_CHARACTERS  = 'strip_control_characters';
    const OPTION_STRIP_DIRECTION_OVERRIDES = 'strip_direction_overrides';
    const OPTION_STRIP_INVALID_UTF8        = 'strip_invalid_utf8';
    const OPTION_STRIP_NULL_BYTES          = 'strip_null_bytes';
    const OPTION_STRIP_ZERO_WIDTH_SPACE    = 'strip_zero_width_space';
    const OPTION_TRIM                      = 'trim';

    // copyright_url options
    const OPTION_COPYRIGHT_URL_MANDATORY                    = 'copyright_url_mandatory';
    const OPTION_COPYRIGHT_URL_USE_IDN                      = 'copyright_url_use_idn';
    const OPTION_COPYRIGHT_URL_CONVERT_HOST_TO_PUNYCODE     = 'copyright_url_convert_host_to_punycode';
    const OPTION_COPYRIGHT_URL_ACCEPT_SUSPICIOUS_HOST       = 'copyright_url_accept_suspicious_host';
    const OPTION_COPYRIGHT_URL_CONVERT_SUSPICIOUS_HOST      = 'copyright_url_convert_suspicious_host';
    const OPTION_COPYRIGHT_URL_DOMAIN_SPOOFCHECKER_CHECKS   = 'copyright_url_domain_spoofchecker_checks';
    const OPTION_COPYRIGHT_URL_ALLOWED_SCHEMES              = 'copyright_url_allowed_schemes';
    const OPTION_COPYRIGHT_URL_SCHEME_SEPARATOR             = 'copyright_url_scheme_separator';
    const OPTION_COPYRIGHT_URL_DEFAULT_SCHEME               = 'copyright_url_default_scheme';
    const OPTION_COPYRIGHT_URL_DEFAULT_USER                 = 'copyright_url_default_user';
    const OPTION_COPYRIGHT_URL_DEFAULT_PASS                 = 'copyright_url_default_pass';
    const OPTION_COPYRIGHT_URL_DEFAULT_PORT                 = 'copyright_url_default_port';
    const OPTION_COPYRIGHT_URL_DEFAULT_PATH                 = 'copyright_url_default_path';
    const OPTION_COPYRIGHT_URL_DEFAULT_QUERY                = 'copyright_url_default_query';
    const OPTION_COPYRIGHT_URL_DEFAULT_FRAGMENT             = 'copyright_url_default_fragment';
    const OPTION_COPYRIGHT_URL_REQUIRE_USER                 = 'copyright_url_require_user';
    const OPTION_COPYRIGHT_URL_REQUIRE_PASS                 = 'copyright_url_require_pass';
    const OPTION_COPYRIGHT_URL_REQUIRE_PORT                 = 'copyright_url_require_port';
    const OPTION_COPYRIGHT_URL_REQUIRE_PATH                 = 'copyright_url_require_path';
    const OPTION_COPYRIGHT_URL_REQUIRE_QUERY                = 'copyright_url_require_query';
    const OPTION_COPYRIGHT_URL_REQUIRE_FRAGMENT             = 'copyright_url_require_fragment';
    const OPTION_COPYRIGHT_URL_FORCE_USER                   = 'copyright_url_force_user';
    const OPTION_COPYRIGHT_URL_FORCE_PASS                   = 'copyright_url_force_pass';
    const OPTION_COPYRIGHT_URL_FORCE_HOST                   = 'copyright_url_force_host';
    const OPTION_COPYRIGHT_URL_FORCE_PORT                   = 'copyright_url_force_port';
    const OPTION_COPYRIGHT_URL_FORCE_PATH                   = 'copyright_url_force_path';
    const OPTION_COPYRIGHT_URL_FORCE_QUERY                  = 'copyright_url_force_query';
    const OPTION_COPYRIGHT_URL_FORCE_FRAGMENT               = 'copyright_url_force_fragment';
    const OPTION_COPYRIGHT_URL_ALLOW_CRLF                   = 'copyright_url_allow_crlf';
    const OPTION_COPYRIGHT_URL_ALLOW_TAB                    = 'copyright_url_allow_tab';
    const OPTION_COPYRIGHT_URL_MAX_LENGTH                   = 'copyright_url_max_length';
    const OPTION_COPYRIGHT_URL_MIN_LENGTH                   = 'copyright_url_min_length';
    const OPTION_COPYRIGHT_URL_NORMALIZE_NEWLINES           = 'copyright_url_normalize_newlines';
    const OPTION_COPYRIGHT_URL_REJECT_INVALID_UTF8          = 'copyright_url_reject_invalid_utf8';
    const OPTION_COPYRIGHT_URL_STRIP_CONTROL_CHARACTERS     = 'copyright_url_strip_control_characters';
    const OPTION_COPYRIGHT_URL_STRIP_DIRECTION_OVERRIDES    = 'copyright_url_strip_direction_overrides';
    const OPTION_COPYRIGHT_URL_STRIP_INVALID_UTF8           = 'copyright_url_strip_invalid_utf8';
    const OPTION_COPYRIGHT_URL_STRIP_NULL_BYTES             = 'copyright_url_strip_null_bytes';
    const OPTION_COPYRIGHT_URL_STRIP_ZERO_WIDTH_SPACE       = 'copyright_url_strip_zero_width_space';
    const OPTION_COPYRIGHT_URL_TRIM                         = 'copyright_url_trim';

    protected $validations = [
        Image::PROPERTY_LOCATION        => TextRule::CLASS,
        Image::PROPERTY_TITLE           => TextRule::CLASS,
        Image::PROPERTY_CAPTION         => TextRule::CLASS,
        Image::PROPERTY_COPYRIGHT       => TextRule::CLASS,
        Image::PROPERTY_COPYRIGHT_URL   => UrlRule::CLASS,
        Image::PROPERTY_SOURCE          => TextRule::CLASS,
        Image::PROPERTY_AOI             => TextRule::CLASS
    ];

    protected function execute($value, EntityInterface $entity = null)
    {
        try {
            if (is_array($value)) {
                if (!empty($value) && !$this->isAssoc($value)) {
                    $this->throwError('non_assoc_array', [ 'value' => $value ], IncidentInterface::CRITICAL);
                    return false;
                }
                $image = Image::createFromArray($value);
            } elseif ($value instanceof Image) {
                $image = Image::createFromArray($value->toNative());
            } else {
                $this->throwError('invalid_type', [ 'value' => $value ], IncidentInterface::CRITICAL);
                return false;
            }

            $incoming_data = $image->toNative();

            $data = [];

            foreach ($this->validations as $property_name => $implementor) {
                $rule = new $implementor(
                    'valid-' . $property_name,
                    $this->getSupportedOptionsFor($implementor, $property_name)
                );

                if (!$rule->apply($incoming_data[$property_name])) {
                    $this->throwIncidentsAsErrors($rule, $property_name);
                    return false;
                }
                $data[$property_name] = $rule->getSanitizedValue();
            }

            // meta data accepts scalar values
            $rule = new KeyValueListRule('valid-meta-data', $this->getMetaDataOptions());
            if (!$rule->apply($incoming_data[Image::PROPERTY_META_DATA])) {
                $this->throwIncidentsAsErrors($rule, Image::PROPERTY_META_DATA);
                return false;
            }
            $data[Image::PROPERTY_META_DATA] = $rule->getSanitizedValue();

            // set the sanitized new image data
            $this->setSanitizedValue(Image::createFromArray($data));
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
