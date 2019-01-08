<?php

namespace Trellis\Runtime\Attribute\Asset;

use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Attribute\HandlesFileInterface;
use Trellis\Runtime\Attribute\HasComplexValueInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\RuleList;

/**
 * An asset (file metadata including a location).
 */
class AssetAttribute extends Attribute implements HandlesFileInterface, HasComplexValueInterface
{
    // filesize options
    const OPTION_FILESIZE_MIN_VALUE                       = AssetRule::OPTION_FILESIZE_MIN_VALUE;
    const OPTION_FILESIZE_MAX_VALUE                       = AssetRule::OPTION_FILESIZE_MAX_VALUE;

    // filename options
    const OPTION_FILENAME_MAX_LENGTH                      = AssetRule::OPTION_FILENAME_MAX_LENGTH;
    const OPTION_FILENAME_MIN_LENGTH                      = AssetRule::OPTION_FILENAME_MIN_LENGTH;
    const OPTION_FILENAME_REPLACE_SPECIAL_CHARS           = AssetRule::OPTION_FILENAME_REPLACE_SPECIAL_CHARS;
    const OPTION_FILENAME_REPLACE_WITH                    = AssetRule::OPTION_FILENAME_REPLACE_WITH;
    const OPTION_FILENAME_LOWERCASE                       = AssetRule::OPTION_FILENAME_LOWERCASE;

    // mimetype options
    const OPTION_MIMETYPE_ALLOW_CRLF                      = AssetRule::OPTION_MIMETYPE_ALLOW_CRLF;
    const OPTION_MIMETYPE_ALLOW_TAB                       = AssetRule::OPTION_MIMETYPE_ALLOW_TAB;
    const OPTION_MIMETYPE_MAX_LENGTH                      = AssetRule::OPTION_MIMETYPE_MAX_LENGTH;
    const OPTION_MIMETYPE_MIN_LENGTH                      = AssetRule::OPTION_MIMETYPE_MIN_LENGTH;
    const OPTION_MIMETYPE_NORMALIZE_NEWLINES              = AssetRule::OPTION_MIMETYPE_NORMALIZE_NEWLINES;
    const OPTION_MIMETYPE_REJECT_INVALID_UTF8             = AssetRule::OPTION_MIMETYPE_REJECT_INVALID_UTF8;
    const OPTION_MIMETYPE_STRIP_CONTROL_CHARACTERS        = AssetRule::OPTION_MIMETYPE_STRIP_CONTROL_CHARACTERS;
    const OPTION_MIMETYPE_STRIP_DIRECTION_OVERRIDES       = AssetRule::OPTION_MIMETYPE_STRIP_DIRECTION_OVERRIDES;
    const OPTION_MIMETYPE_STRIP_INVALID_UTF8              = AssetRule::OPTION_MIMETYPE_STRIP_INVALID_UTF8;
    const OPTION_MIMETYPE_STRIP_NULL_BYTES                = AssetRule::OPTION_MIMETYPE_STRIP_NULL_BYTES;
    const OPTION_MIMETYPE_STRIP_ZERO_WIDTH_SPACE          = AssetRule::OPTION_MIMETYPE_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_MIMETYPE_TRIM                            = AssetRule::OPTION_MIMETYPE_TRIM;

    // restrict metadata to certain keys or values or key-value pairs
    const OPTION_METADATA_ALLOWED_KEYS                   = AssetRule::OPTION_METADATA_ALLOWED_KEYS;
    const OPTION_METADATA_ALLOWED_VALUES                 = AssetRule::OPTION_METADATA_ALLOWED_VALUES;
    const OPTION_METADATA_ALLOWED_PAIRS                  = AssetRule::OPTION_METADATA_ALLOWED_PAIRS;
    /**
     * Option to define that metadata values must be of a certain scalar type.
     */
    const OPTION_METADATA_VALUE_TYPE                     = AssetRule::OPTION_METADATA_VALUE_TYPE;
    const METADATA_VALUE_TYPE_BOOLEAN                    = AssetRule::METADATA_VALUE_TYPE_BOOLEAN;
    const METADATA_VALUE_TYPE_INTEGER                    = AssetRule::METADATA_VALUE_TYPE_INTEGER;
    const METADATA_VALUE_TYPE_FLOAT                      = AssetRule::METADATA_VALUE_TYPE_FLOAT;
    const METADATA_VALUE_TYPE_SCALAR                     = AssetRule::METADATA_VALUE_TYPE_SCALAR;
    const METADATA_VALUE_TYPE_TEXT                       = AssetRule::METADATA_VALUE_TYPE_TEXT;
    const OPTION_METADATA_MAX_VALUE                      = AssetRule::OPTION_METADATA_MAX_VALUE;
    const OPTION_METADATA_MIN_VALUE                      = AssetRule::OPTION_METADATA_MIN_VALUE;
    // text options for metadata
    const OPTION_METADATA_ALLOW_CRLF                     = AssetRule::OPTION_METADATA_ALLOW_CRLF;
    const OPTION_METADATA_ALLOW_TAB                      = AssetRule::OPTION_METADATA_ALLOW_TAB;
    const OPTION_METADATA_MAX_LENGTH                     = AssetRule::OPTION_METADATA_MAX_LENGTH;
    const OPTION_METADATA_MIN_LENGTH                     = AssetRule::OPTION_METADATA_MIN_LENGTH;
    const OPTION_METADATA_NORMALIZE_NEWLINES             = AssetRule::OPTION_METADATA_NORMALIZE_NEWLINES;
    const OPTION_METADATA_REJECT_INVALID_UTF8            = AssetRule::OPTION_METADATA_REJECT_INVALID_UTF8;
    const OPTION_METADATA_STRIP_CONTROL_CHARACTERS       = AssetRule::OPTION_METADATA_STRIP_CONTROL_CHARACTERS;
    const OPTION_METADATA_STRIP_DIRECTION_OVERRIDES      = AssetRule::OPTION_METADATA_STRIP_DIRECTION_OVERRIDES;
    const OPTION_METADATA_STRIP_INVALID_UTF8             = AssetRule::OPTION_METADATA_STRIP_INVALID_UTF8;
    const OPTION_METADATA_STRIP_NULL_BYTES               = AssetRule::OPTION_METADATA_STRIP_NULL_BYTES;
    const OPTION_METADATA_STRIP_ZERO_WIDTH_SPACE         = AssetRule::OPTION_METADATA_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_METADATA_TRIM                           = AssetRule::OPTION_METADATA_TRIM;
    // integer options for metadata
    const OPTION_METADATA_ALLOW_HEX                      = AssetRule::OPTION_METADATA_ALLOW_HEX;
    const OPTION_METADATA_ALLOW_OCTAL                    = AssetRule::OPTION_METADATA_ALLOW_OCTAL;
    const OPTION_METADATA_MAX_INTEGER_VALUE              = AssetRule::OPTION_METADATA_MAX_INTEGER_VALUE;
    const OPTION_METADATA_MIN_INTEGER_VALUE              = AssetRule::OPTION_METADATA_MIN_INTEGER_VALUE;
    // float options for metadata
    const OPTION_METADATA_ALLOW_THOUSAND_SEPARATOR       = AssetRule::OPTION_METADATA_ALLOW_THOUSAND_SEPARATOR;
    const OPTION_METADATA_PRECISION_DIGITS               = AssetRule::OPTION_METADATA_PRECISION_DIGITS;
    const OPTION_METADATA_ALLOW_INFINITY                 = AssetRule::OPTION_METADATA_ALLOW_INFINITY;
    const OPTION_METADATA_ALLOW_NAN                      = AssetRule::OPTION_METADATA_ALLOW_NAN;
    const OPTION_METADATA_MAX_FLOAT_VALUE                = AssetRule::OPTION_METADATA_MAX_FLOAT_VALUE;
    const OPTION_METADATA_MIN_FLOAT_VALUE                = AssetRule::OPTION_METADATA_MIN_FLOAT_VALUE;

    // copyright_url options
    const OPTION_COPYRIGHT_URL_MANDATORY                  = AssetRule::OPTION_COPYRIGHT_URL_MANDATORY;
    const OPTION_COPYRIGHT_URL_USE_IDN                    = AssetRule::OPTION_COPYRIGHT_URL_USE_IDN;
    const OPTION_COPYRIGHT_URL_CONVERT_HOST_TO_PUNYCODE   = AssetRule::OPTION_COPYRIGHT_URL_CONVERT_HOST_TO_PUNYCODE;
    const OPTION_COPYRIGHT_URL_ACCEPT_SUSPICIOUS_HOST     = AssetRule::OPTION_COPYRIGHT_URL_ACCEPT_SUSPICIOUS_HOST;
    const OPTION_COPYRIGHT_URL_CONVERT_SUSPICIOUS_HOST    = AssetRule::OPTION_COPYRIGHT_URL_CONVERT_SUSPICIOUS_HOST;
    const OPTION_COPYRIGHT_URL_DOMAIN_SPOOFCHECKER_CHECKS = AssetRule::OPTION_COPYRIGHT_URL_DOMAIN_SPOOFCHECKER_CHECKS;
    const OPTION_COPYRIGHT_URL_ALLOWED_SCHEMES            = AssetRule::OPTION_COPYRIGHT_URL_ALLOWED_SCHEMES;
    const OPTION_COPYRIGHT_URL_SCHEME_SEPARATOR           = AssetRule::OPTION_COPYRIGHT_URL_SCHEME_SEPARATOR;
    const OPTION_COPYRIGHT_URL_DEFAULT_SCHEME             = AssetRule::OPTION_COPYRIGHT_URL_DEFAULT_SCHEME;
    const OPTION_COPYRIGHT_URL_DEFAULT_USER               = AssetRule::OPTION_COPYRIGHT_URL_DEFAULT_USER;
    const OPTION_COPYRIGHT_URL_DEFAULT_PASS               = AssetRule::OPTION_COPYRIGHT_URL_DEFAULT_PASS;
    const OPTION_COPYRIGHT_URL_DEFAULT_PORT               = AssetRule::OPTION_COPYRIGHT_URL_DEFAULT_PORT;
    const OPTION_COPYRIGHT_URL_DEFAULT_PATH               = AssetRule::OPTION_COPYRIGHT_URL_DEFAULT_PATH;
    const OPTION_COPYRIGHT_URL_DEFAULT_QUERY              = AssetRule::OPTION_COPYRIGHT_URL_DEFAULT_QUERY;
    const OPTION_COPYRIGHT_URL_DEFAULT_FRAGMENT           = AssetRule::OPTION_COPYRIGHT_URL_DEFAULT_FRAGMENT;
    const OPTION_COPYRIGHT_URL_REQUIRE_USER               = AssetRule::OPTION_COPYRIGHT_URL_REQUIRE_USER;
    const OPTION_COPYRIGHT_URL_REQUIRE_PASS               = AssetRule::OPTION_COPYRIGHT_URL_REQUIRE_PASS;
    const OPTION_COPYRIGHT_URL_REQUIRE_PORT               = AssetRule::OPTION_COPYRIGHT_URL_REQUIRE_PORT;
    const OPTION_COPYRIGHT_URL_REQUIRE_PATH               = AssetRule::OPTION_COPYRIGHT_URL_REQUIRE_PATH;
    const OPTION_COPYRIGHT_URL_REQUIRE_QUERY              = AssetRule::OPTION_COPYRIGHT_URL_REQUIRE_QUERY;
    const OPTION_COPYRIGHT_URL_REQUIRE_FRAGMENT           = AssetRule::OPTION_COPYRIGHT_URL_REQUIRE_FRAGMENT;
    const OPTION_COPYRIGHT_URL_FORCE_USER                 = AssetRule::OPTION_COPYRIGHT_URL_FORCE_USER;
    const OPTION_COPYRIGHT_URL_FORCE_PASS                 = AssetRule::OPTION_COPYRIGHT_URL_FORCE_PASS;
    const OPTION_COPYRIGHT_URL_FORCE_HOST                 = AssetRule::OPTION_COPYRIGHT_URL_FORCE_HOST;
    const OPTION_COPYRIGHT_URL_FORCE_PORT                 = AssetRule::OPTION_COPYRIGHT_URL_FORCE_PORT;
    const OPTION_COPYRIGHT_URL_FORCE_PATH                 = AssetRule::OPTION_COPYRIGHT_URL_FORCE_PATH;
    const OPTION_COPYRIGHT_URL_FORCE_QUERY                = AssetRule::OPTION_COPYRIGHT_URL_FORCE_QUERY;
    const OPTION_COPYRIGHT_URL_FORCE_FRAGMENT             = AssetRule::OPTION_COPYRIGHT_URL_FORCE_FRAGMENT;
    const OPTION_COPYRIGHT_URL_ALLOW_CRLF                 = AssetRule::OPTION_COPYRIGHT_URL_ALLOW_CRLF;
    const OPTION_COPYRIGHT_URL_ALLOW_TAB                  = AssetRule::OPTION_COPYRIGHT_URL_ALLOW_TAB;
    const OPTION_COPYRIGHT_URL_MAX_LENGTH                 = AssetRule::OPTION_COPYRIGHT_URL_MAX_LENGTH;
    const OPTION_COPYRIGHT_URL_MIN_LENGTH                 = AssetRule::OPTION_COPYRIGHT_URL_MIN_LENGTH;
    const OPTION_COPYRIGHT_URL_NORMALIZE_NEWLINES         = AssetRule::OPTION_COPYRIGHT_URL_NORMALIZE_NEWLINES;
    const OPTION_COPYRIGHT_URL_REJECT_INVALID_UTF8        = AssetRule::OPTION_COPYRIGHT_URL_REJECT_INVALID_UTF8;
    const OPTION_COPYRIGHT_URL_STRIP_CONTROL_CHARACTERS   = AssetRule::OPTION_COPYRIGHT_URL_STRIP_CONTROL_CHARACTERS;
    const OPTION_COPYRIGHT_URL_STRIP_DIRECTION_OVERRIDES  = AssetRule::OPTION_COPYRIGHT_URL_STRIP_DIRECTION_OVERRIDES;
    const OPTION_COPYRIGHT_URL_STRIP_INVALID_UTF8         = AssetRule::OPTION_COPYRIGHT_URL_STRIP_INVALID_UTF8;
    const OPTION_COPYRIGHT_URL_STRIP_NULL_BYTES           = AssetRule::OPTION_COPYRIGHT_URL_STRIP_NULL_BYTES;
    const OPTION_COPYRIGHT_URL_STRIP_ZERO_WIDTH_SPACE     = AssetRule::OPTION_COPYRIGHT_URL_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_COPYRIGHT_URL_TRIM                       = AssetRule::OPTION_COPYRIGHT_URL_TRIM;

    // location options
    const OPTION_LOCATION_ALLOW_CRLF                      = AssetRule::OPTION_LOCATION_ALLOW_CRLF;
    const OPTION_LOCATION_ALLOW_TAB                       = AssetRule::OPTION_LOCATION_ALLOW_TAB;
    const OPTION_LOCATION_MAX_LENGTH                      = AssetRule::OPTION_LOCATION_MAX_LENGTH;
    const OPTION_LOCATION_MIN_LENGTH                      = AssetRule::OPTION_LOCATION_MIN_LENGTH;
    const OPTION_LOCATION_NORMALIZE_NEWLINES              = AssetRule::OPTION_LOCATION_NORMALIZE_NEWLINES;
    const OPTION_LOCATION_REJECT_INVALID_UTF8             = AssetRule::OPTION_LOCATION_REJECT_INVALID_UTF8;
    const OPTION_LOCATION_STRIP_CONTROL_CHARACTERS        = AssetRule::OPTION_LOCATION_STRIP_CONTROL_CHARACTERS;
    const OPTION_LOCATION_STRIP_DIRECTION_OVERRIDES       = AssetRule::OPTION_LOCATION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_LOCATION_STRIP_INVALID_UTF8              = AssetRule::OPTION_LOCATION_STRIP_INVALID_UTF8;
    const OPTION_LOCATION_STRIP_NULL_BYTES                = AssetRule::OPTION_LOCATION_STRIP_NULL_BYTES;
    const OPTION_LOCATION_STRIP_ZERO_WIDTH_SPACE          = AssetRule::OPTION_LOCATION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_LOCATION_TRIM                            = AssetRule::OPTION_LOCATION_TRIM;

    // title options
    const OPTION_TITLE_ALLOW_CRLF                         = AssetRule::OPTION_TITLE_ALLOW_CRLF;
    const OPTION_TITLE_ALLOW_TAB                          = AssetRule::OPTION_TITLE_ALLOW_TAB;
    const OPTION_TITLE_MAX_LENGTH                         = AssetRule::OPTION_TITLE_MAX_LENGTH;
    const OPTION_TITLE_MIN_LENGTH                         = AssetRule::OPTION_TITLE_MIN_LENGTH;
    const OPTION_TITLE_NORMALIZE_NEWLINES                 = AssetRule::OPTION_TITLE_NORMALIZE_NEWLINES;
    const OPTION_TITLE_REJECT_INVALID_UTF8                = AssetRule::OPTION_TITLE_REJECT_INVALID_UTF8;
    const OPTION_TITLE_STRIP_CONTROL_CHARACTERS           = AssetRule::OPTION_TITLE_STRIP_CONTROL_CHARACTERS;
    const OPTION_TITLE_STRIP_DIRECTION_OVERRIDES          = AssetRule::OPTION_TITLE_STRIP_DIRECTION_OVERRIDES;
    const OPTION_TITLE_STRIP_INVALID_UTF8                 = AssetRule::OPTION_TITLE_STRIP_INVALID_UTF8;
    const OPTION_TITLE_STRIP_NULL_BYTES                   = AssetRule::OPTION_TITLE_STRIP_NULL_BYTES;
    const OPTION_TITLE_STRIP_ZERO_WIDTH_SPACE             = AssetRule::OPTION_TITLE_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TITLE_TRIM                               = AssetRule::OPTION_TITLE_TRIM;

    // caption options
    const OPTION_CAPTION_ALLOW_CRLF                       = AssetRule::OPTION_CAPTION_ALLOW_CRLF;
    const OPTION_CAPTION_ALLOW_TAB                        = AssetRule::OPTION_CAPTION_ALLOW_TAB;
    const OPTION_CAPTION_MAX_LENGTH                       = AssetRule::OPTION_CAPTION_MAX_LENGTH;
    const OPTION_CAPTION_MIN_LENGTH                       = AssetRule::OPTION_CAPTION_MIN_LENGTH;
    const OPTION_CAPTION_NORMALIZE_NEWLINES               = AssetRule::OPTION_CAPTION_NORMALIZE_NEWLINES;
    const OPTION_CAPTION_REJECT_INVALID_UTF8              = AssetRule::OPTION_CAPTION_REJECT_INVALID_UTF8;
    const OPTION_CAPTION_STRIP_CONTROL_CHARACTERS         = AssetRule::OPTION_CAPTION_STRIP_CONTROL_CHARACTERS;
    const OPTION_CAPTION_STRIP_DIRECTION_OVERRIDES        = AssetRule::OPTION_CAPTION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_CAPTION_STRIP_INVALID_UTF8               = AssetRule::OPTION_CAPTION_STRIP_INVALID_UTF8;
    const OPTION_CAPTION_STRIP_NULL_BYTES                 = AssetRule::OPTION_CAPTION_STRIP_NULL_BYTES;
    const OPTION_CAPTION_STRIP_ZERO_WIDTH_SPACE           = AssetRule::OPTION_CAPTION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_CAPTION_TRIM                             = AssetRule::OPTION_CAPTION_TRIM;

    // copyright options
    const OPTION_COPYRIGHT_ALLOW_CRLF                     = AssetRule::OPTION_COPYRIGHT_ALLOW_CRLF;
    const OPTION_COPYRIGHT_ALLOW_TAB                      = AssetRule::OPTION_COPYRIGHT_ALLOW_TAB;
    const OPTION_COPYRIGHT_MAX_LENGTH                     = AssetRule::OPTION_COPYRIGHT_MAX_LENGTH;
    const OPTION_COPYRIGHT_MIN_LENGTH                     = AssetRule::OPTION_COPYRIGHT_MIN_LENGTH;
    const OPTION_COPYRIGHT_NORMALIZE_NEWLINES             = AssetRule::OPTION_COPYRIGHT_NORMALIZE_NEWLINES;
    const OPTION_COPYRIGHT_REJECT_INVALID_UTF8            = AssetRule::OPTION_COPYRIGHT_REJECT_INVALID_UTF8;
    const OPTION_COPYRIGHT_STRIP_CONTROL_CHARACTERS       = AssetRule::OPTION_COPYRIGHT_STRIP_CONTROL_CHARACTERS;
    const OPTION_COPYRIGHT_STRIP_DIRECTION_OVERRIDES      = AssetRule::OPTION_COPYRIGHT_STRIP_DIRECTION_OVERRIDES;
    const OPTION_COPYRIGHT_STRIP_INVALID_UTF8             = AssetRule::OPTION_COPYRIGHT_STRIP_INVALID_UTF8;
    const OPTION_COPYRIGHT_STRIP_NULL_BYTES               = AssetRule::OPTION_COPYRIGHT_STRIP_NULL_BYTES;
    const OPTION_COPYRIGHT_STRIP_ZERO_WIDTH_SPACE         = AssetRule::OPTION_COPYRIGHT_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_COPYRIGHT_TRIM                           = AssetRule::OPTION_COPYRIGHT_TRIM;

    // source options
    const OPTION_SOURCE_ALLOW_CRLF                        = AssetRule::OPTION_SOURCE_ALLOW_CRLF;
    const OPTION_SOURCE_ALLOW_TAB                         = AssetRule::OPTION_SOURCE_ALLOW_TAB;
    const OPTION_SOURCE_MAX_LENGTH                        = AssetRule::OPTION_SOURCE_MAX_LENGTH;
    const OPTION_SOURCE_MIN_LENGTH                        = AssetRule::OPTION_SOURCE_MIN_LENGTH;
    const OPTION_SOURCE_NORMALIZE_NEWLINES                = AssetRule::OPTION_SOURCE_NORMALIZE_NEWLINES;
    const OPTION_SOURCE_REJECT_INVALID_UTF8               = AssetRule::OPTION_SOURCE_REJECT_INVALID_UTF8;
    const OPTION_SOURCE_STRIP_CONTROL_CHARACTERS          = AssetRule::OPTION_SOURCE_STRIP_CONTROL_CHARACTERS;
    const OPTION_SOURCE_STRIP_DIRECTION_OVERRIDES         = AssetRule::OPTION_SOURCE_STRIP_DIRECTION_OVERRIDES;
    const OPTION_SOURCE_STRIP_INVALID_UTF8                = AssetRule::OPTION_SOURCE_STRIP_INVALID_UTF8;
    const OPTION_SOURCE_STRIP_NULL_BYTES                  = AssetRule::OPTION_SOURCE_STRIP_NULL_BYTES;
    const OPTION_SOURCE_STRIP_ZERO_WIDTH_SPACE            = AssetRule::OPTION_SOURCE_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_SOURCE_TRIM                              = AssetRule::OPTION_SOURCE_TRIM;

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new AssetRule('valid-asset', $options));

        return $rules;
    }

    /**
     * Returns the property name that is used to store a file identifier.
     *
     * This property may be used for input field names in HTML and should then
     * be used in the file metadata value object as a property name for storing
     * a relative file path or similar.
     *
     * @return string property name
     */
    public function getFileLocationPropertyName()
    {
        return Asset::PROPERTY_LOCATION;
    }

    /**
     * @return string property name for filesize in byte of the handled file
     */
    public function getFileSizePropertyName()
    {
        return Asset::PROPERTY_FILESIZE;
    }

    /**
     * @return string property name for filename storage of the handled file
     */
    public function getFileNamePropertyName()
    {
        return Asset::PROPERTY_FILENAME;
    }

    /**
     * @return string property name for mimetype storage of the handled file
     */
    public function getFileMimetypePropertyName()
    {
        return Asset::PROPERTY_MIMETYPE;
    }

    /**
     * @return string type identifier of file type handled by the attribute
     */
    public function getFiletypeName()
    {
        return self::FILETYPE_FILE;
    }
}
