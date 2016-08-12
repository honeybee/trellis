<?php

namespace Trellis\Runtime\Attribute\Asset;

use Exception;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\Type\FloatRule;
use Trellis\Runtime\Validator\Rule\Type\IntegerRule;
use Trellis\Runtime\Validator\Rule\Type\KeyValueListRule;
use Trellis\Runtime\Validator\Rule\Type\SanitizedFilenameRule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Trellis\Runtime\Validator\Rule\Type\UrlRule;

class AssetRule extends Rule
{
    // integer rule options for filesize property
    const OPTION_FILESIZE_MIN_VALUE = 'filesize_min_value';
    const OPTION_FILESIZE_MAX_VALUE = 'filesize_max_value';

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

    // rule options for filename property
    const OPTION_FILENAME_MAX_LENGTH                        = 'filename_max_length';
    const OPTION_FILENAME_MIN_LENGTH                        = 'filename_min_length';
    const OPTION_FILENAME_REPLACE_SPECIAL_CHARS             = 'filename_replace_special_chars';
    const OPTION_FILENAME_REPLACE_WITH                      = 'filename_replace_with';
    const OPTION_FILENAME_LOWERCASE                         = 'filename_lowercase';

    // text rule options for mimetype property
    const OPTION_MIMETYPE_ALLOW_CRLF                        = 'mimetype_allow_crlf';
    const OPTION_MIMETYPE_ALLOW_TAB                         = 'mimetype_allow_tab';
    const OPTION_MIMETYPE_MAX_LENGTH                        = 'mimetype_max_length';
    const OPTION_MIMETYPE_MIN_LENGTH                        = 'mimetype_min_length';
    const OPTION_MIMETYPE_NORMALIZE_NEWLINES                = 'mimetype_normalize_newlines';
    const OPTION_MIMETYPE_REJECT_INVALID_UTF8               = 'mimetype_reject_invalid_utf8';
    const OPTION_MIMETYPE_STRIP_CONTROL_CHARACTERS          = 'mimetype_strip_control_characters';
    const OPTION_MIMETYPE_STRIP_DIRECTION_OVERRIDES         = 'mimetype_strip_direction_overrides';
    const OPTION_MIMETYPE_STRIP_INVALID_UTF8                = 'mimetype_strip_invalid_utf8';
    const OPTION_MIMETYPE_STRIP_NULL_BYTES                  = 'mimetype_strip_null_bytes';
    const OPTION_MIMETYPE_STRIP_ZERO_WIDTH_SPACE            = 'mimetype_strip_zero_width_space';
    const OPTION_MIMETYPE_TRIM                              = 'mimetype_trim';

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

    // restrict metadata to certain keys or values or key-value pairs
    const OPTION_METADATA_ALLOWED_KEYS                     = 'metadata_allowed_keys';
    const OPTION_METADATA_ALLOWED_VALUES                   = 'metadata_allowed_values';
    const OPTION_METADATA_ALLOWED_PAIRS                    = 'metadata_allowed_pairs';

    /**
     * Option to define that metadata values must be of a certain scalar type.
     */
    const OPTION_METADATA_VALUE_TYPE                       = 'metadata_value_type';

    const METADATA_VALUE_TYPE_BOOLEAN                      = 'boolean';
    const METADATA_VALUE_TYPE_INTEGER                      = 'integer';
    const METADATA_VALUE_TYPE_FLOAT                        = 'float';
    const METADATA_VALUE_TYPE_SCALAR                       = 'scalar'; // default; any of int, float, bool or string
    const METADATA_VALUE_TYPE_TEXT                         = 'text';

    const OPTION_METADATA_MAX_VALUE                        = 'metadata_max_value'; // when type is float or integer
    const OPTION_METADATA_MIN_VALUE                        = 'metadata_min_value'; // when type is float or integer

    // text rule options for metadata
    const OPTION_METADATA_ALLOW_CRLF                       = 'metadata_allow_crlf';
    const OPTION_METADATA_ALLOW_TAB                        = 'metadata_allow_tab';
    const OPTION_METADATA_MAX_LENGTH                       = 'metadata_max_length';
    const OPTION_METADATA_MIN_LENGTH                       = 'metadata_min_length';
    const OPTION_METADATA_NORMALIZE_NEWLINES               = 'metadata_normalize_newlines';
    const OPTION_METADATA_REJECT_INVALID_UTF8              = 'metadata_reject_invalid_utf8';
    const OPTION_METADATA_STRIP_CONTROL_CHARACTERS         = 'metadata_strip_control_characters';
    const OPTION_METADATA_STRIP_DIRECTION_OVERRIDES        = 'metadata_strip_direction_overrides';
    const OPTION_METADATA_STRIP_INVALID_UTF8               = 'metadata_strip_invalid_utf8';
    const OPTION_METADATA_STRIP_NULL_BYTES                 = 'metadata_strip_null_bytes';
    const OPTION_METADATA_STRIP_ZERO_WIDTH_SPACE           = 'metadata_strip_zero_width_space';
    const OPTION_METADATA_TRIM                             = 'metadata_trim';

    // integer rule options for metadata
    const OPTION_METADATA_ALLOW_HEX                        = 'metadata_allow_hex';
    const OPTION_METADATA_ALLOW_OCTAL                      = 'metadata_allow_octal';
    const OPTION_METADATA_MAX_INTEGER_VALUE                = 'metadata_max_integer_value';
    const OPTION_METADATA_MIN_INTEGER_VALUE                = 'metadata_min_integer_value';

    // float rule options for metadata
    const OPTION_METADATA_ALLOW_THOUSAND_SEPARATOR         = 'metadata_allow_thousand_separator';
    const OPTION_METADATA_PRECISION_DIGITS                 = 'metadata_precision_digits';
    const OPTION_METADATA_ALLOW_INFINITY                   = 'metadata_allow_infinity';
    const OPTION_METADATA_ALLOW_NAN                        = 'metadata_allow_nan';
    const OPTION_METADATA_MAX_FLOAT_VALUE                  = 'metadata_max_float_value';
    const OPTION_METADATA_MIN_FLOAT_VALUE                  = 'metadata_min_float_value';

    protected $validations = [
        Asset::PROPERTY_LOCATION        => TextRule::CLASS,
        Asset::PROPERTY_TITLE           => TextRule::CLASS,
        Asset::PROPERTY_CAPTION         => TextRule::CLASS,
        Asset::PROPERTY_COPYRIGHT       => TextRule::CLASS,
        Asset::PROPERTY_COPYRIGHT_URL   => UrlRule::CLASS,
        Asset::PROPERTY_SOURCE          => TextRule::CLASS,
        Asset::PROPERTY_FILESIZE        => IntegerRule::CLASS,
        Asset::PROPERTY_FILENAME        => SanitizedFilenameRule::CLASS,
        Asset::PROPERTY_MIMETYPE        => TextRule::CLASS
    ];

    protected $metadata_options = [
        self::OPTION_METADATA_ALLOWED_KEYS,
        self::OPTION_METADATA_ALLOWED_VALUES,
        self::OPTION_METADATA_ALLOWED_PAIRS,
        self::OPTION_METADATA_VALUE_TYPE,
        self::OPTION_METADATA_MAX_VALUE,
        self::OPTION_METADATA_MIN_VALUE,

        self::OPTION_METADATA_ALLOW_CRLF,
        self::OPTION_METADATA_ALLOW_TAB,
        self::OPTION_METADATA_MAX_LENGTH,
        self::OPTION_METADATA_MIN_LENGTH,
        self::OPTION_METADATA_NORMALIZE_NEWLINES,
        self::OPTION_METADATA_REJECT_INVALID_UTF8,
        self::OPTION_METADATA_STRIP_CONTROL_CHARACTERS,
        self::OPTION_METADATA_STRIP_DIRECTION_OVERRIDES,
        self::OPTION_METADATA_STRIP_INVALID_UTF8,
        self::OPTION_METADATA_STRIP_NULL_BYTES,
        self::OPTION_METADATA_STRIP_ZERO_WIDTH_SPACE,
        self::OPTION_METADATA_TRIM,

        self::OPTION_METADATA_ALLOW_HEX,
        self::OPTION_METADATA_ALLOW_OCTAL,
        self::OPTION_METADATA_MAX_INTEGER_VALUE,
        self::OPTION_METADATA_MIN_INTEGER_VALUE,

        self::OPTION_METADATA_ALLOW_THOUSAND_SEPARATOR,
        self::OPTION_METADATA_PRECISION_DIGITS,
        self::OPTION_METADATA_ALLOW_INFINITY,
        self::OPTION_METADATA_ALLOW_NAN,
        self::OPTION_METADATA_MAX_FLOAT_VALUE,
        self::OPTION_METADATA_MIN_FLOAT_VALUE
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
            $rule = new KeyValueListRule('valid-metadata', $this->getMetadataOptions());
            if (!$rule->apply($incoming_data[Asset::PROPERTY_METADATA])) {
                $this->throwIncidentsAsErrors($rule, Asset::PROPERTY_METADATA);
                return false;
            }
            $data[Asset::PROPERTY_METADATA] = $rule->getSanitizedValue();

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

    protected function getMetadataOptions()
    {
        $options = $this->getOptions();

        $value_type = self::METADATA_VALUE_TYPE_SCALAR;
        if (array_key_exists(self::OPTION_METADATA_VALUE_TYPE, $options)) {
            $value_type = $options[self::OPTION_METADATA_VALUE_TYPE];
        }

        // when a specific value type is forced use the specific min/max option value instead of the generic one
        if ($value_type === self::METADATA_VALUE_TYPE_FLOAT) {
            if (array_key_exists(self::OPTION_METADATA_MIN_FLOAT_VALUE, $options)) {
                $options[FloatRule::OPTION_MIN_VALUE] = $options[self::OPTION_METADATA_MIN_FLOAT_VALUE];
            }

            if (array_key_exists(self::OPTION_METADATA_MAX_FLOAT_VALUE, $options)) {
                $options[FloatRule::OPTION_MAX_VALUE] = $options[self::OPTION_METADATA_MAX_FLOAT_VALUE];
            }
        } elseif ($value_type === self::METADATA_VALUE_TYPE_INTEGER) {
            if (array_key_exists(self::OPTION_METADATA_MIN_INTEGER_VALUE, $options)) {
                $options[IntegerRule::OPTION_MIN_VALUE] = $options[self::OPTION_METADATA_MIN_INTEGER_VALUE];
            }

            if (array_key_exists(self::OPTION_METADATA_MAX_INTEGER_VALUE, $options)) {
                $options[IntegerRule::OPTION_MAX_VALUE] = $options[self::OPTION_METADATA_MAX_INTEGER_VALUE];
            }
        }

        $kvl_options = [];

        // map all metadata options to normal KeyValueListRule supported options
        foreach ($this->metadata_options as $name) {
            if (array_key_exists($name, $options)) {
                $opt_name = str_replace('metadata_', '', $name);
                $kvl_options[$opt_name] = $options[$name];
            }
        }

        return $kvl_options;
    }
}
