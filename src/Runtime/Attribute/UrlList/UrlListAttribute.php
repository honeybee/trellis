<?php

namespace Trellis\Runtime\Attribute\UrlList;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Attribute\ListAttribute;
use Trellis\Runtime\Validator\Result\IncidentInterface;

/**
 * A list of strings (urls).
 */
class UrlListAttribute extends ListAttribute
{
    const OPTION_USE_IDN = UrlListRule::OPTION_USE_IDN;
    const OPTION_CONVERT_HOST_TO_PUNYCODE = UrlListRule::OPTION_CONVERT_HOST_TO_PUNYCODE;

    const OPTION_ACCEPT_SUSPICIOUS_HOST = UrlListRule::OPTION_ACCEPT_SUSPICIOUS_HOST;
    const OPTION_CONVERT_SUSPICIOUS_HOST = UrlListRule::OPTION_CONVERT_SUSPICIOUS_HOST;
    const OPTION_DOMAIN_SPOOFCHECKER_CHECKS = UrlListRule::OPTION_DOMAIN_SPOOFCHECKER_CHECKS;

    const OPTION_ALLOWED_SCHEMES = UrlListRule::OPTION_ALLOWED_SCHEMES;

    const OPTION_SCHEME_SEPARATOR = UrlListRule::OPTION_SCHEME_SEPARATOR;

    const OPTION_DEFAULT_SCHEME = UrlListRule::OPTION_DEFAULT_SCHEME;
    const OPTION_DEFAULT_USER = UrlListRule::OPTION_DEFAULT_USER;
    const OPTION_DEFAULT_PASS = UrlListRule::OPTION_DEFAULT_PASS;
    const OPTION_DEFAULT_PORT = UrlListRule::OPTION_DEFAULT_PORT;
    const OPTION_DEFAULT_PATH = UrlListRule::OPTION_DEFAULT_PATH;
    const OPTION_DEFAULT_QUERY = UrlListRule::OPTION_DEFAULT_QUERY;
    const OPTION_DEFAULT_FRAGMENT = UrlListRule::OPTION_DEFAULT_FRAGMENT;

    const OPTION_REQUIRE_USER = UrlListRule::OPTION_REQUIRE_USER;
    const OPTION_REQUIRE_PASS = UrlListRule::OPTION_REQUIRE_PASS;
    const OPTION_REQUIRE_PORT = UrlListRule::OPTION_REQUIRE_PORT;
    const OPTION_REQUIRE_PATH = UrlListRule::OPTION_REQUIRE_PATH;
    const OPTION_REQUIRE_QUERY = UrlListRule::OPTION_REQUIRE_QUERY;
    const OPTION_REQUIRE_FRAGMENT = UrlListRule::OPTION_REQUIRE_FRAGMENT;

    const OPTION_FORCE_USER = UrlListRule::OPTION_FORCE_USER;
    const OPTION_FORCE_PASS = UrlListRule::OPTION_FORCE_PASS;
    const OPTION_FORCE_HOST = UrlListRule::OPTION_FORCE_HOST;
    const OPTION_FORCE_PORT = UrlListRule::OPTION_FORCE_PORT;
    const OPTION_FORCE_PATH = UrlListRule::OPTION_FORCE_PATH;
    const OPTION_FORCE_QUERY = UrlListRule::OPTION_FORCE_QUERY;
    const OPTION_FORCE_FRAGMENT = UrlListRule::OPTION_FORCE_FRAGMENT;

    const OPTION_ALLOW_CRLF                 = UrlListRule::OPTION_ALLOW_CRLF;
    const OPTION_ALLOW_TAB                  = UrlListRule::OPTION_ALLOW_TAB;
    const OPTION_MAX_LENGTH                 = UrlListRule::OPTION_MAX_LENGTH;
    const OPTION_MIN_LENGTH                 = UrlListRule::OPTION_MIN_LENGTH;
    const OPTION_NORMALIZE_NEWLINES         = UrlListRule::OPTION_NORMALIZE_NEWLINES;
    const OPTION_REJECT_INVALID_UTF8        = UrlListRule::OPTION_REJECT_INVALID_UTF8;
    const OPTION_STRIP_CONTROL_CHARACTERS   = UrlListRule::OPTION_STRIP_CONTROL_CHARACTERS;
    const OPTION_STRIP_DIRECTION_OVERRIDES  = UrlListRule::OPTION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_STRIP_INVALID_UTF8         = UrlListRule::OPTION_STRIP_INVALID_UTF8;
    const OPTION_STRIP_NULL_BYTES           = UrlListRule::OPTION_STRIP_NULL_BYTES;
    const OPTION_STRIP_ZERO_WIDTH_SPACE     = UrlListRule::OPTION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TRIM                       = UrlListRule::OPTION_TRIM;

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new UrlListRule('valid-url-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
