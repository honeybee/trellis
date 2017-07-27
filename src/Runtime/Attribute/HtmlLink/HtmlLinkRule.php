<?php

namespace Trellis\Runtime\Attribute\HtmlLink;

use Exception;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\Type\BooleanRule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Trellis\Runtime\Validator\Rule\Type\UrlRule;

class HtmlLinkRule extends Rule
{
    // text rule options for text property
    const OPTION_TEXT_ALLOW_CRLF                        = 'text_allow_crlf';
    const OPTION_TEXT_ALLOW_TAB                         = 'text_allow_tab';
    const OPTION_TEXT_MAX_LENGTH                        = 'text_max_length';
    const OPTION_TEXT_MIN_LENGTH                        = 'text_min_length';
    const OPTION_TEXT_NORMALIZE_NEWLINES                = 'text_normalize_newlines';
    const OPTION_TEXT_REJECT_INVALID_UTF8               = 'text_reject_invalid_utf8';
    const OPTION_TEXT_STRIP_CONTROL_CHARACTERS          = 'text_strip_control_characters';
    const OPTION_TEXT_STRIP_DIRECTION_OVERRIDES         = 'text_strip_direction_overrides';
    const OPTION_TEXT_STRIP_INVALID_UTF8                = 'text_strip_invalid_utf8';
    const OPTION_TEXT_STRIP_NULL_BYTES                  = 'text_strip_null_bytes';
    const OPTION_TEXT_STRIP_ZERO_WIDTH_SPACE            = 'text_strip_zero_width_space';
    const OPTION_TEXT_TRIM                              = 'text_trim';

    // text rule options for title property
    const OPTION_TITLE_ALLOW_CRLF                       = 'title_allow_crlf';
    const OPTION_TITLE_ALLOW_TAB                        = 'title_allow_tab';
    const OPTION_TITLE_MAX_LENGTH                       = 'title_max_length';
    const OPTION_TITLE_MIN_LENGTH                       = 'title_min_length';
    const OPTION_TITLE_NORMALIZE_NEWLINES               = 'title_normalize_newlines';
    const OPTION_TITLE_REJECT_INVALID_UTF8              = 'title_reject_invalid_utf8';
    const OPTION_TITLE_STRIP_CONTROL_CHARACTERS         = 'title_strip_control_characters';
    const OPTION_TITLE_STRIP_DIRECTION_OVERRIDES        = 'title_strip_direction_overrides';
    const OPTION_TITLE_STRIP_INVALID_UTF8               = 'title_strip_invalid_utf8';
    const OPTION_TITLE_STRIP_NULL_BYTES                 = 'title_strip_null_bytes';
    const OPTION_TITLE_STRIP_ZERO_WIDTH_SPACE           = 'title_strip_zero_width_space';
    const OPTION_TITLE_TRIM                             = 'title_trim';

    // text rule options for hreflang property
    const OPTION_HREFLANG_ALLOW_CRLF                    = 'hreflang_allow_crlf';
    const OPTION_HREFLANG_ALLOW_TAB                     = 'hreflang_allow_tab';
    const OPTION_HREFLANG_MAX_LENGTH                    = 'hreflang_max_length';
    const OPTION_HREFLANG_MIN_LENGTH                    = 'hreflang_min_length';
    const OPTION_HREFLANG_NORMALIZE_NEWLINES            = 'hreflang_normalize_newlines';
    const OPTION_HREFLANG_REJECT_INVALID_UTF8           = 'hreflang_reject_invalid_utf8';
    const OPTION_HREFLANG_STRIP_CONTROL_CHARACTERS      = 'hreflang_strip_control_characters';
    const OPTION_HREFLANG_STRIP_DIRECTION_OVERRIDES     = 'hreflang_strip_direction_overrides';
    const OPTION_HREFLANG_STRIP_INVALID_UTF8            = 'hreflang_strip_invalid_utf8';
    const OPTION_HREFLANG_STRIP_NULL_BYTES              = 'hreflang_strip_null_bytes';
    const OPTION_HREFLANG_STRIP_ZERO_WIDTH_SPACE        = 'hreflang_strip_zero_width_space';
    const OPTION_HREFLANG_TRIM                          = 'hreflang_trim';

    // text rule options for rel property
    const OPTION_REL_ALLOW_CRLF                         = 'rel_allow_crlf';
    const OPTION_REL_ALLOW_TAB                          = 'rel_allow_tab';
    const OPTION_REL_MAX_LENGTH                         = 'rel_max_length';
    const OPTION_REL_MIN_LENGTH                         = 'rel_min_length';
    const OPTION_REL_NORMALIZE_NEWLINES                 = 'rel_normalize_newlines';
    const OPTION_REL_REJECT_INVALID_UTF8                = 'rel_reject_invalid_utf8';
    const OPTION_REL_STRIP_CONTROL_CHARACTERS           = 'rel_strip_control_characters';
    const OPTION_REL_STRIP_DIRECTION_OVERRIDES          = 'rel_strip_direction_overrides';
    const OPTION_REL_STRIP_INVALID_UTF8                 = 'rel_strip_invalid_utf8';
    const OPTION_REL_STRIP_NULL_BYTES                   = 'rel_strip_null_bytes';
    const OPTION_REL_STRIP_ZERO_WIDTH_SPACE             = 'rel_strip_zero_width_space';
    const OPTION_REL_TRIM                               = 'rel_trim';

    // text rule options for target property
    const OPTION_TARGET_ALLOW_CRLF                      = 'target_allow_crlf';
    const OPTION_TARGET_ALLOW_TAB                       = 'target_allow_tab';
    const OPTION_TARGET_MAX_LENGTH                      = 'target_max_length';
    const OPTION_TARGET_MIN_LENGTH                      = 'target_min_length';
    const OPTION_TARGET_NORMALIZE_NEWLINES              = 'target_normalize_newlines';
    const OPTION_TARGET_REJECT_INVALID_UTF8             = 'target_reject_invalid_utf8';
    const OPTION_TARGET_STRIP_CONTROL_CHARACTERS        = 'target_strip_control_characters';
    const OPTION_TARGET_STRIP_DIRECTION_OVERRIDES       = 'target_strip_direction_overrides';
    const OPTION_TARGET_STRIP_INVALID_UTF8              = 'target_strip_invalid_utf8';
    const OPTION_TARGET_STRIP_NULL_BYTES                = 'target_strip_null_bytes';
    const OPTION_TARGET_STRIP_ZERO_WIDTH_SPACE          = 'target_strip_zero_width_space';
    const OPTION_TARGET_TRIM                            = 'target_trim';

    // href url options
    const OPTION_HREF_MANDATORY                         = 'href_mandatory';
    const OPTION_HREF_USE_IDN                           = 'href_use_idn';
    const OPTION_HREF_CONVERT_HOST_TO_PUNYCODE          = 'href_convert_host_to_punycode';
    const OPTION_HREF_ACCEPT_SUSPICIOUS_HOST            = 'href_accept_suspicious_host';
    const OPTION_HREF_CONVERT_SUSPICIOUS_HOST           = 'href_convert_suspicious_host';
    const OPTION_HREF_DOMAIN_SPOOFCHECKER_CHECKS        = 'href_domain_spoofchecker_checks';
    const OPTION_HREF_ALLOWED_SCHEMES                   = 'href_allowed_schemes';
    const OPTION_HREF_SCHEME_SEPARATOR                  = 'href_scheme_separator';
    const OPTION_HREF_DEFAULT_SCHEME                    = 'href_default_scheme';
    const OPTION_HREF_DEFAULT_USER                      = 'href_default_user';
    const OPTION_HREF_DEFAULT_PASS                      = 'href_default_pass';
    const OPTION_HREF_DEFAULT_PORT                      = 'href_default_port';
    const OPTION_HREF_DEFAULT_PATH                      = 'href_default_path';
    const OPTION_HREF_DEFAULT_QUERY                     = 'href_default_query';
    const OPTION_HREF_DEFAULT_FRAGMENT                  = 'href_default_fragment';
    const OPTION_HREF_REQUIRE_USER                      = 'href_require_user';
    const OPTION_HREF_REQUIRE_PASS                      = 'href_require_pass';
    const OPTION_HREF_REQUIRE_PORT                      = 'href_require_port';
    const OPTION_HREF_REQUIRE_PATH                      = 'href_require_path';
    const OPTION_HREF_REQUIRE_QUERY                     = 'href_require_query';
    const OPTION_HREF_REQUIRE_FRAGMENT                  = 'href_require_fragment';
    const OPTION_HREF_FORCE_USER                        = 'href_force_user';
    const OPTION_HREF_FORCE_PASS                        = 'href_force_pass';
    const OPTION_HREF_FORCE_HOST                        = 'href_force_host';
    const OPTION_HREF_FORCE_PORT                        = 'href_force_port';
    const OPTION_HREF_FORCE_PATH                        = 'href_force_path';
    const OPTION_HREF_FORCE_QUERY                       = 'href_force_query';
    const OPTION_HREF_FORCE_FRAGMENT                    = 'href_force_fragment';
    const OPTION_HREF_ALLOW_CRLF                        = 'href_allow_crlf';
    const OPTION_HREF_ALLOW_TAB                         = 'href_allow_tab';
    const OPTION_HREF_MAX_LENGTH                        = 'href_max_length';
    const OPTION_HREF_MIN_LENGTH                        = 'href_min_length';
    const OPTION_HREF_NORMALIZE_NEWLINES                = 'href_normalize_newlines';
    const OPTION_HREF_REJECT_INVALID_UTF8               = 'href_reject_invalid_utf8';
    const OPTION_HREF_STRIP_CONTROL_CHARACTERS          = 'href_strip_control_characters';
    const OPTION_HREF_STRIP_DIRECTION_OVERRIDES         = 'href_strip_direction_overrides';
    const OPTION_HREF_STRIP_INVALID_UTF8                = 'href_strip_invalid_utf8';
    const OPTION_HREF_STRIP_NULL_BYTES                  = 'href_strip_null_bytes';
    const OPTION_HREF_STRIP_ZERO_WIDTH_SPACE            = 'href_strip_zero_width_space';
    const OPTION_HREF_TRIM                              = 'href_trim';

    protected $validations = [
        HtmlLink::PROPERTY_HREF        => UrlRule::CLASS,
        HtmlLink::PROPERTY_TEXT        => TextRule::CLASS,
        HtmlLink::PROPERTY_TITLE       => TextRule::CLASS,
        HtmlLink::PROPERTY_HREFLANG    => TextRule::CLASS,
        HtmlLink::PROPERTY_REL         => TextRule::CLASS,
        HtmlLink::PROPERTY_TARGET      => TextRule::CLASS,
        HtmlLink::PROPERTY_DOWNLOAD    => BooleanRule::CLASS,
    ];

    protected function execute($value, EntityInterface $entity = null)
    {
        try {
            if (is_array($value)) {
                if (!empty($value) && !$this->isAssoc($value)) {
                    $this->throwError('non_assoc_array', [ 'value' => $value ], IncidentInterface::CRITICAL);
                    return false;
                }
                $link = HtmlLink::createFromArray($value);
            } elseif ($value instanceof HtmlLink) {
                $link = HtmlLink::createFromArray($value->toNative());
            } else {
                $this->throwError('invalid_type', [ 'value' => $value ], IncidentInterface::CRITICAL);
                return false;
            }

            $incoming_data = $link->toNative();

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

            // set the sanitized new link data
            $this->setSanitizedValue(HtmlLink::createFromArray($data));
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
}
