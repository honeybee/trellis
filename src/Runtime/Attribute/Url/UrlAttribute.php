<?php

namespace Trellis\Runtime\Attribute\Url;

use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\UrlRule;

class UrlAttribute extends Attribute
{
    const OPTION_MANDATORY                  = UrlRule::OPTION_MANDATORY;

    //
    // UrlRule options
    //

    const OPTION_USE_IDN                    = UrlRule::OPTION_USE_IDN;
    const OPTION_CONVERT_HOST_TO_PUNYCODE   = UrlRule::OPTION_CONVERT_HOST_TO_PUNYCODE;

    const OPTION_ACCEPT_SUSPICIOUS_HOST     = UrlRule::OPTION_ACCEPT_SUSPICIOUS_HOST;
    const OPTION_CONVERT_SUSPICIOUS_HOST    = UrlRule::OPTION_CONVERT_SUSPICIOUS_HOST;
    const OPTION_DOMAIN_SPOOFCHECKER_CHECKS = UrlRule::OPTION_DOMAIN_SPOOFCHECKER_CHECKS; // integer value!

    const OPTION_ALLOWED_SCHEMES            = UrlRule::OPTION_ALLOWED_SCHEMES;

    const OPTION_SCHEME_SEPARATOR           = UrlRule::OPTION_SCHEME_SEPARATOR;

    const OPTION_DEFAULT_SCHEME             = UrlRule::OPTION_DEFAULT_SCHEME;
    const OPTION_DEFAULT_USER               = UrlRule::OPTION_DEFAULT_USER;
    const OPTION_DEFAULT_PASS               = UrlRule::OPTION_DEFAULT_PASS;
    const OPTION_DEFAULT_PORT               = UrlRule::OPTION_DEFAULT_PASS;
    const OPTION_DEFAULT_PATH               = UrlRule::OPTION_DEFAULT_PATH;
    const OPTION_DEFAULT_QUERY              = UrlRule::OPTION_DEFAULT_QUERY;
    const OPTION_DEFAULT_FRAGMENT           = UrlRule::OPTION_DEFAULT_FRAGMENT;

    const OPTION_REQUIRE_USER               = UrlRule::OPTION_REQUIRE_USER;
    const OPTION_REQUIRE_PASS               = UrlRule::OPTION_REQUIRE_PASS;
    const OPTION_REQUIRE_PORT               = UrlRule::OPTION_REQUIRE_PORT;
    const OPTION_REQUIRE_PATH               = UrlRule::OPTION_REQUIRE_PATH;
    const OPTION_REQUIRE_QUERY              = UrlRule::OPTION_REQUIRE_QUERY;
    const OPTION_REQUIRE_FRAGMENT           = UrlRule::OPTION_REQUIRE_FRAGMENT;

    const OPTION_FORCE_USER                 = UrlRule::OPTION_FORCE_USER;
    const OPTION_FORCE_PASS                 = UrlRule::OPTION_FORCE_PASS;
    const OPTION_FORCE_HOST                 = UrlRule::OPTION_FORCE_HOST;
    const OPTION_FORCE_PORT                 = UrlRule::OPTION_FORCE_PORT;
    const OPTION_FORCE_PATH                 = UrlRule::OPTION_FORCE_PATH;
    const OPTION_FORCE_QUERY                = UrlRule::OPTION_FORCE_QUERY;
    const OPTION_FORCE_FRAGMENT             = UrlRule::OPTION_FORCE_FRAGMENT;

    //const OPTION_ALLOW_PROTOCOL_RELATIVE_URL = 'allow_protocol_relative_url';

    //
    // TextRule options that may be used for validating the url input string
    //

    const OPTION_ALLOW_CRLF                 = UrlRule::OPTION_ALLOW_CRLF;
    const OPTION_ALLOW_TAB                  = UrlRule::OPTION_ALLOW_TAB;
    const OPTION_MAX_LENGTH                 = UrlRule::OPTION_MAX_LENGTH;
    const OPTION_MIN_LENGTH                 = UrlRule::OPTION_MIN_LENGTH;
    const OPTION_NORMALIZE_NEWLINES         = UrlRule::OPTION_NORMALIZE_NEWLINES;
    const OPTION_REJECT_INVALID_UTF8        = UrlRule::OPTION_REJECT_INVALID_UTF8;
    const OPTION_STRIP_CONTROL_CHARACTERS   = UrlRule::OPTION_STRIP_CONTROL_CHARACTERS;
    const OPTION_STRIP_DIRECTION_OVERRIDES  = UrlRule::OPTION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_STRIP_INVALID_UTF8         = UrlRule::OPTION_STRIP_INVALID_UTF8;
    const OPTION_STRIP_NULL_BYTES           = UrlRule::OPTION_STRIP_NULL_BYTES;
    const OPTION_STRIP_ZERO_WIDTH_SPACE     = UrlRule::OPTION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TRIM                       = UrlRule::OPTION_TRIM;

    public function getNullValue()
    {
        return '';
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(
            new UrlRule('valid-url', $options)
        );

        return $rules;
    }
}
