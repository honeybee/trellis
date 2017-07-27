<?php

namespace Trellis\Runtime\Attribute\HtmlLinkList;

use Trellis\Runtime\Attribute\ListAttribute;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLinkRule;

/**
 * A list of HtmlLinks.
 */
class HtmlLinkListAttribute extends ListAttribute
{
    // href URL options
    const OPTION_HREF_MANDATORY                  = HtmlLinkRule::OPTION_HREF_MANDATORY;
    const OPTION_HREF_USE_IDN                    = HtmlLinkRule::OPTION_HREF_USE_IDN;
    const OPTION_HREF_CONVERT_HOST_TO_PUNYCODE   = HtmlLinkRule::OPTION_HREF_CONVERT_HOST_TO_PUNYCODE;
    const OPTION_HREF_ACCEPT_SUSPICIOUS_HOST     = HtmlLinkRule::OPTION_HREF_ACCEPT_SUSPICIOUS_HOST;
    const OPTION_HREF_CONVERT_SUSPICIOUS_HOST    = HtmlLinkRule::OPTION_HREF_CONVERT_SUSPICIOUS_HOST;
    const OPTION_HREF_DOMAIN_SPOOFCHECKER_CHECKS = HtmlLinkRule::OPTION_HREF_DOMAIN_SPOOFCHECKER_CHECKS;
    const OPTION_HREF_ALLOWED_SCHEMES            = HtmlLinkRule::OPTION_HREF_ALLOWED_SCHEMES;
    const OPTION_HREF_SCHEME_SEPARATOR           = HtmlLinkRule::OPTION_HREF_SCHEME_SEPARATOR;
    const OPTION_HREF_DEFAULT_SCHEME             = HtmlLinkRule::OPTION_HREF_DEFAULT_SCHEME;
    const OPTION_HREF_DEFAULT_USER               = HtmlLinkRule::OPTION_HREF_DEFAULT_USER;
    const OPTION_HREF_DEFAULT_PASS               = HtmlLinkRule::OPTION_HREF_DEFAULT_PASS;
    const OPTION_HREF_DEFAULT_PORT               = HtmlLinkRule::OPTION_HREF_DEFAULT_PORT;
    const OPTION_HREF_DEFAULT_PATH               = HtmlLinkRule::OPTION_HREF_DEFAULT_PATH;
    const OPTION_HREF_DEFAULT_QUERY              = HtmlLinkRule::OPTION_HREF_DEFAULT_QUERY;
    const OPTION_HREF_DEFAULT_FRAGMENT           = HtmlLinkRule::OPTION_HREF_DEFAULT_FRAGMENT;
    const OPTION_HREF_REQUIRE_USER               = HtmlLinkRule::OPTION_HREF_REQUIRE_USER;
    const OPTION_HREF_REQUIRE_PASS               = HtmlLinkRule::OPTION_HREF_REQUIRE_PASS;
    const OPTION_HREF_REQUIRE_PORT               = HtmlLinkRule::OPTION_HREF_REQUIRE_PORT;
    const OPTION_HREF_REQUIRE_PATH               = HtmlLinkRule::OPTION_HREF_REQUIRE_PATH;
    const OPTION_HREF_REQUIRE_QUERY              = HtmlLinkRule::OPTION_HREF_REQUIRE_QUERY;
    const OPTION_HREF_REQUIRE_FRAGMENT           = HtmlLinkRule::OPTION_HREF_REQUIRE_FRAGMENT;
    const OPTION_HREF_FORCE_USER                 = HtmlLinkRule::OPTION_HREF_FORCE_USER;
    const OPTION_HREF_FORCE_PASS                 = HtmlLinkRule::OPTION_HREF_FORCE_PASS;
    const OPTION_HREF_FORCE_HOST                 = HtmlLinkRule::OPTION_HREF_FORCE_HOST;
    const OPTION_HREF_FORCE_PORT                 = HtmlLinkRule::OPTION_HREF_FORCE_PORT;
    const OPTION_HREF_FORCE_PATH                 = HtmlLinkRule::OPTION_HREF_FORCE_PATH;
    const OPTION_HREF_FORCE_QUERY                = HtmlLinkRule::OPTION_HREF_FORCE_QUERY;
    const OPTION_HREF_FORCE_FRAGMENT             = HtmlLinkRule::OPTION_HREF_FORCE_FRAGMENT;
    const OPTION_HREF_ALLOW_CRLF                 = HtmlLinkRule::OPTION_HREF_ALLOW_CRLF;
    const OPTION_HREF_ALLOW_TAB                  = HtmlLinkRule::OPTION_HREF_ALLOW_TAB;
    const OPTION_HREF_MAX_LENGTH                 = HtmlLinkRule::OPTION_HREF_MAX_LENGTH;
    const OPTION_HREF_MIN_LENGTH                 = HtmlLinkRule::OPTION_HREF_MIN_LENGTH;
    const OPTION_HREF_NORMALIZE_NEWLINES         = HtmlLinkRule::OPTION_HREF_NORMALIZE_NEWLINES;
    const OPTION_HREF_REJECT_INVALID_UTF8        = HtmlLinkRule::OPTION_HREF_REJECT_INVALID_UTF8;
    const OPTION_HREF_STRIP_CONTROL_CHARACTERS   = HtmlLinkRule::OPTION_HREF_STRIP_CONTROL_CHARACTERS;
    const OPTION_HREF_STRIP_DIRECTION_OVERRIDES  = HtmlLinkRule::OPTION_HREF_STRIP_DIRECTION_OVERRIDES;
    const OPTION_HREF_STRIP_INVALID_UTF8         = HtmlLinkRule::OPTION_HREF_STRIP_INVALID_UTF8;
    const OPTION_HREF_STRIP_NULL_BYTES           = HtmlLinkRule::OPTION_HREF_STRIP_NULL_BYTES;
    const OPTION_HREF_STRIP_ZERO_WIDTH_SPACE     = HtmlLinkRule::OPTION_HREF_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_HREF_TRIM                       = HtmlLinkRule::OPTION_HREF_TRIM;

    // text options
    const OPTION_TEXT_ALLOW_CRLF                      = HtmlLinkRule::OPTION_TEXT_ALLOW_CRLF;
    const OPTION_TEXT_ALLOW_TAB                       = HtmlLinkRule::OPTION_TEXT_ALLOW_TAB;
    const OPTION_TEXT_MAX_LENGTH                      = HtmlLinkRule::OPTION_TEXT_MAX_LENGTH;
    const OPTION_TEXT_MIN_LENGTH                      = HtmlLinkRule::OPTION_TEXT_MIN_LENGTH;
    const OPTION_TEXT_NORMALIZE_NEWLINES              = HtmlLinkRule::OPTION_TEXT_NORMALIZE_NEWLINES;
    const OPTION_TEXT_REJECT_INVALID_UTF8             = HtmlLinkRule::OPTION_TEXT_REJECT_INVALID_UTF8;
    const OPTION_TEXT_STRIP_CONTROL_CHARACTERS        = HtmlLinkRule::OPTION_TEXT_STRIP_CONTROL_CHARACTERS;
    const OPTION_TEXT_STRIP_DIRECTION_OVERRIDES       = HtmlLinkRule::OPTION_TEXT_STRIP_DIRECTION_OVERRIDES;
    const OPTION_TEXT_STRIP_INVALID_UTF8              = HtmlLinkRule::OPTION_TEXT_STRIP_INVALID_UTF8;
    const OPTION_TEXT_STRIP_NULL_BYTES                = HtmlLinkRule::OPTION_TEXT_STRIP_NULL_BYTES;
    const OPTION_TEXT_STRIP_ZERO_WIDTH_SPACE          = HtmlLinkRule::OPTION_TEXT_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TEXT_TRIM                            = HtmlLinkRule::OPTION_TEXT_TRIM;

    // title options
    const OPTION_TITLE_ALLOW_CRLF                         = HtmlLinkRule::OPTION_TITLE_ALLOW_CRLF;
    const OPTION_TITLE_ALLOW_TAB                          = HtmlLinkRule::OPTION_TITLE_ALLOW_TAB;
    const OPTION_TITLE_MAX_LENGTH                         = HtmlLinkRule::OPTION_TITLE_MAX_LENGTH;
    const OPTION_TITLE_MIN_LENGTH                         = HtmlLinkRule::OPTION_TITLE_MIN_LENGTH;
    const OPTION_TITLE_NORMALIZE_NEWLINES                 = HtmlLinkRule::OPTION_TITLE_NORMALIZE_NEWLINES;
    const OPTION_TITLE_REJECT_INVALID_UTF8                = HtmlLinkRule::OPTION_TITLE_REJECT_INVALID_UTF8;
    const OPTION_TITLE_STRIP_CONTROL_CHARACTERS           = HtmlLinkRule::OPTION_TITLE_STRIP_CONTROL_CHARACTERS;
    const OPTION_TITLE_STRIP_DIRECTION_OVERRIDES          = HtmlLinkRule::OPTION_TITLE_STRIP_DIRECTION_OVERRIDES;
    const OPTION_TITLE_STRIP_INVALID_UTF8                 = HtmlLinkRule::OPTION_TITLE_STRIP_INVALID_UTF8;
    const OPTION_TITLE_STRIP_NULL_BYTES                   = HtmlLinkRule::OPTION_TITLE_STRIP_NULL_BYTES;
    const OPTION_TITLE_STRIP_ZERO_WIDTH_SPACE             = HtmlLinkRule::OPTION_TITLE_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TITLE_TRIM                               = HtmlLinkRule::OPTION_TITLE_TRIM;

    // hreflang options
    const OPTION_HREFLANG_ALLOW_CRLF                       = HtmlLinkRule::OPTION_HREFLANG_ALLOW_CRLF;
    const OPTION_HREFLANG_ALLOW_TAB                        = HtmlLinkRule::OPTION_HREFLANG_ALLOW_TAB;
    const OPTION_HREFLANG_MAX_LENGTH                       = HtmlLinkRule::OPTION_HREFLANG_MAX_LENGTH;
    const OPTION_HREFLANG_MIN_LENGTH                       = HtmlLinkRule::OPTION_HREFLANG_MIN_LENGTH;
    const OPTION_HREFLANG_NORMALIZE_NEWLINES               = HtmlLinkRule::OPTION_HREFLANG_NORMALIZE_NEWLINES;
    const OPTION_HREFLANG_REJECT_INVALID_UTF8              = HtmlLinkRule::OPTION_HREFLANG_REJECT_INVALID_UTF8;
    const OPTION_HREFLANG_STRIP_CONTROL_CHARACTERS         = HtmlLinkRule::OPTION_HREFLANG_STRIP_CONTROL_CHARACTERS;
    const OPTION_HREFLANG_STRIP_DIRECTION_OVERRIDES        = HtmlLinkRule::OPTION_HREFLANG_STRIP_DIRECTION_OVERRIDES;
    const OPTION_HREFLANG_STRIP_INVALID_UTF8               = HtmlLinkRule::OPTION_HREFLANG_STRIP_INVALID_UTF8;
    const OPTION_HREFLANG_STRIP_NULL_BYTES                 = HtmlLinkRule::OPTION_HREFLANG_STRIP_NULL_BYTES;
    const OPTION_HREFLANG_STRIP_ZERO_WIDTH_SPACE           = HtmlLinkRule::OPTION_HREFLANG_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_HREFLANG_TRIM                             = HtmlLinkRule::OPTION_HREFLANG_TRIM;

    // rel options
    const OPTION_REL_ALLOW_CRLF                     = HtmlLinkRule::OPTION_REL_ALLOW_CRLF;
    const OPTION_REL_ALLOW_TAB                      = HtmlLinkRule::OPTION_REL_ALLOW_TAB;
    const OPTION_REL_MAX_LENGTH                     = HtmlLinkRule::OPTION_REL_MAX_LENGTH;
    const OPTION_REL_MIN_LENGTH                     = HtmlLinkRule::OPTION_REL_MIN_LENGTH;
    const OPTION_REL_NORMALIZE_NEWLINES             = HtmlLinkRule::OPTION_REL_NORMALIZE_NEWLINES;
    const OPTION_REL_REJECT_INVALID_UTF8            = HtmlLinkRule::OPTION_REL_REJECT_INVALID_UTF8;
    const OPTION_REL_STRIP_CONTROL_CHARACTERS       = HtmlLinkRule::OPTION_REL_STRIP_CONTROL_CHARACTERS;
    const OPTION_REL_STRIP_DIRECTION_OVERRIDES      = HtmlLinkRule::OPTION_REL_STRIP_DIRECTION_OVERRIDES;
    const OPTION_REL_STRIP_INVALID_UTF8             = HtmlLinkRule::OPTION_REL_STRIP_INVALID_UTF8;
    const OPTION_REL_STRIP_NULL_BYTES               = HtmlLinkRule::OPTION_REL_STRIP_NULL_BYTES;
    const OPTION_REL_STRIP_ZERO_WIDTH_SPACE         = HtmlLinkRule::OPTION_REL_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_REL_TRIM                           = HtmlLinkRule::OPTION_REL_TRIM;

    // target options
    const OPTION_TARGET_ALLOW_CRLF                        = HtmlLinkRule::OPTION_TARGET_ALLOW_CRLF;
    const OPTION_TARGET_ALLOW_TAB                         = HtmlLinkRule::OPTION_TARGET_ALLOW_TAB;
    const OPTION_TARGET_MAX_LENGTH                        = HtmlLinkRule::OPTION_TARGET_MAX_LENGTH;
    const OPTION_TARGET_MIN_LENGTH                        = HtmlLinkRule::OPTION_TARGET_MIN_LENGTH;
    const OPTION_TARGET_NORMALIZE_NEWLINES                = HtmlLinkRule::OPTION_TARGET_NORMALIZE_NEWLINES;
    const OPTION_TARGET_REJECT_INVALID_UTF8               = HtmlLinkRule::OPTION_TARGET_REJECT_INVALID_UTF8;
    const OPTION_TARGET_STRIP_CONTROL_CHARACTERS          = HtmlLinkRule::OPTION_TARGET_STRIP_CONTROL_CHARACTERS;
    const OPTION_TARGET_STRIP_DIRECTION_OVERRIDES         = HtmlLinkRule::OPTION_TARGET_STRIP_DIRECTION_OVERRIDES;
    const OPTION_TARGET_STRIP_INVALID_UTF8                = HtmlLinkRule::OPTION_TARGET_STRIP_INVALID_UTF8;
    const OPTION_TARGET_STRIP_NULL_BYTES                  = HtmlLinkRule::OPTION_TARGET_STRIP_NULL_BYTES;
    const OPTION_TARGET_STRIP_ZERO_WIDTH_SPACE            = HtmlLinkRule::OPTION_TARGET_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TARGET_TRIM                              = HtmlLinkRule::OPTION_TARGET_TRIM;

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new HtmlLinkListRule('valid-html-link-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
