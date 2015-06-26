<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Common\Error\RuntimeException;
use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Spoofchecker;

class UrlRule extends Rule
{
    const OPTION_MANDATORY = 'mandatory';

    //
    // UrlRule options
    //

    const OPTION_USE_IDN = 'use_idn';
    const OPTION_CONVERT_HOST_TO_PUNYCODE = 'convert_host_to_punycode';

    const OPTION_ACCEPT_SUSPICIOUS_HOST = 'accept_suspicious_host';
    const OPTION_CONVERT_SUSPICIOUS_HOST = 'convert_suspicious_host';
    const OPTION_DOMAIN_SPOOFCHECKER_CHECKS = 'domain_spoofchecker_checks'; // should be an integer value!

    const OPTION_ALLOWED_SCHEMES = 'allowed_schemes';

    const OPTION_SCHEME_SEPARATOR = 'scheme_separator';

    const OPTION_DEFAULT_SCHEME = 'default_scheme';
    const OPTION_DEFAULT_USER = 'default_user';
    const OPTION_DEFAULT_PASS = 'default_pass';
    const OPTION_DEFAULT_PORT = 'default_port';
    const OPTION_DEFAULT_PATH = 'default_path';
    const OPTION_DEFAULT_QUERY = 'default_query';
    const OPTION_DEFAULT_FRAGMENT = 'default_fragment';

    const OPTION_REQUIRE_USER = 'require_user';
    const OPTION_REQUIRE_PASS = 'require_pass';
    const OPTION_REQUIRE_PORT = 'require_port';
    const OPTION_REQUIRE_PATH = 'require_path';
    const OPTION_REQUIRE_QUERY = 'require_query';
    const OPTION_REQUIRE_FRAGMENT = 'require_fragment';

    const OPTION_FORCE_USER = 'force_user';
    const OPTION_FORCE_PASS = 'force_pass';
    const OPTION_FORCE_HOST = 'force_host';
    const OPTION_FORCE_PORT = 'force_port';
    const OPTION_FORCE_PATH = 'force_path';
    const OPTION_FORCE_QUERY = 'force_query';
    const OPTION_FORCE_FRAGMENT = 'force_fragment';

    //const OPTION_ALLOW_PROTOCOL_RELATIVE_URL = 'allow_protocol_relative_url';

    //
    // TextRule options that may be used for validating the url input string
    //

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

    public function __construct($name, array $options = [])
    {
        // use sensible default max length for URLs
        if (!array_key_exists(self::OPTION_MAX_LENGTH, $options)) {
            // http://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers
            $options[self::OPTION_MAX_LENGTH] = 2048;
        }

        if (!array_key_exists(self::OPTION_REJECT_INVALID_UTF8, $options)) {
            $options[self::OPTION_REJECT_INVALID_UTF8] = true;
        }

        if (!array_key_exists(self::OPTION_TRIM, $options)) {
            $options[self::OPTION_TRIM] = true;
        }

        if (!array_key_exists(self::OPTION_STRIP_CONTROL_CHARACTERS, $options)) {
            $options[self::OPTION_STRIP_CONTROL_CHARACTERS] = true;
        }

        if (!array_key_exists(self::OPTION_ALLOW_CRLF, $options)) {
            $options[self::OPTION_ALLOW_CRLF] = false;
        }

        if (!array_key_exists(self::OPTION_ALLOW_TAB, $options)) {
            $options[self::OPTION_ALLOW_TAB] = false;
        }

        parent::__construct($name, $options);
    }

    protected function execute($value, EntityInterface $entity = null)
    {
        if (!is_string($value)) {
            $this->throwError('non_string_value', [ 'value' => $value ], IncidentInterface::CRITICAL);
            return false;
        }

        $null_value = $this->getOption(AttributeInterface::OPTION_NULL_VALUE, '');
        $mandatory = $this->toBoolean($this->getOption(self::OPTION_MANDATORY, false));
        if (!$mandatory && $value === $null_value) {
            // parse_url with empty string doesn't return false but 'path' being an empty string
            $this->setSanitizedValue($null_value);
            return true;
        }

        $text_rule = new TextRule('text', $this->getOptions());
        $is_valid = $text_rule->apply($value);
        if (!$is_valid) {
            foreach ($text_rule->getIncidents() as $incident) {
                $this->throwError($incident->getName(), $incident->getParameters(), $incident->getSeverity());
            }
            return false;
        }

        // we now have a valid string, that might be some kind of URL
        $val = $text_rule->getSanitizedValue();

        // default scheme to add if it's missing
        $default_scheme = $this->getOption(self::OPTION_DEFAULT_SCHEME, '');

        // try to parse the string as URL
        $raw_parts = parse_url($val);
        if ($raw_parts === false) {
            $this->throwError('parse_error', [ 'value' => $val ]);
            return false;
        }

        // parse_url returns [ 'path' => 'localhost' ] for 'localhost' or '123.123.123.123'
        // scheme and host are missing, might be a string like: 'test.de/foo/bar' or 'localhost' or '194.123.45.167'…
        if (!array_key_exists('host', $raw_parts) && !array_key_exists('scheme', $raw_parts)) {
            $val = $default_scheme . $this->getOption(self::OPTION_SCHEME_SEPARATOR, '://') . $val;
        }

        // reevaluate the new url value and hope it's now a valid url and the addition didn't do too much harm
        $raw_parts = parse_url($val);
        if ($raw_parts === false) {
            $this->throwError('parse_error', [ 'value' => $val ]);
            return false;
        }

        // validate mandatory host part
        if (!array_key_exists('host', $raw_parts)) {
            $this->throwError('host_missing');
            return false;
        }

        $url_parts = $raw_parts;

        if (!array_key_exists('scheme', $url_parts)) {
            $url_parts['scheme'] = $default_scheme;
        }

        $allowed_schemes = $this->getOption(self::OPTION_ALLOWED_SCHEMES, []);
        if (array_key_exists('scheme', $url_parts) &&
            !empty($allowed_schemes) &&
            !in_array($url_parts['scheme'], $allowed_schemes, true)
        ) {
            $this->throwError(
                'scheme_not_allowed',
                [
                    'value' => $val,
                    'scheme' => $url_parts['scheme'],
                    'allowed_schemes' => $allowed_schemes
                ]
            );
            return false;
        }

        // add default values for parts when they're missing

        if ($this->hasOption(self::OPTION_DEFAULT_USER) && !array_key_exists('user', $url_parts)) {
            $url_parts['user'] = $this->getOption(self::OPTION_DEFAULT_USER);
        }

        if ($this->hasOption(self::OPTION_DEFAULT_PASS) && !array_key_exists('pass', $url_parts)) {
            $url_parts['pass'] = $this->getOption(self::OPTION_DEFAULT_PASS);
        }

        if ($this->hasOption(self::OPTION_DEFAULT_PORT) && !array_key_exists('port', $url_parts)) {
            $url_parts['port'] = $this->getOption(self::OPTION_DEFAULT_PORT);
        }

        if ($this->hasOption(self::OPTION_DEFAULT_PATH) && !array_key_exists('path', $url_parts)) {
            $url_parts['path'] = $this->getOption(self::OPTION_DEFAULT_PATH);
        }

        if ($this->hasOption(self::OPTION_DEFAULT_QUERY) && !array_key_exists('query', $url_parts)) {
            $url_parts['query'] = $this->getOption(self::OPTION_DEFAULT_QUERY);
        }

        if ($this->hasOption(self::OPTION_DEFAULT_FRAGMENT) && !array_key_exists('fragment', $url_parts)) {
            $url_parts['fragment'] = $this->getOption(self::OPTION_DEFAULT_FRAGMENT);
        }

        // force certain values for parts

        if ($this->hasOption(self::OPTION_FORCE_USER)) {
            $url_parts['user'] = $this->getOption(self::OPTION_FORCE_USER);
        }

        if ($this->hasOption(self::OPTION_FORCE_PASS)) {
            $url_parts['pass'] = $this->getOption(self::OPTION_FORCE_PASS);
        }

        if ($this->hasOption(self::OPTION_FORCE_HOST)) {
            $url_parts['host'] = $this->getOption(self::OPTION_FORCE_HOST);
        }

        if ($this->hasOption(self::OPTION_FORCE_PORT)) {
            $url_parts['port'] = $this->getOption(self::OPTION_FORCE_PORT);
        }

        if ($this->hasOption(self::OPTION_FORCE_PATH)) {
            $url_parts['path'] = $this->getOption(self::OPTION_FORCE_PATH);
        }

        if ($this->hasOption(self::OPTION_FORCE_QUERY)) {
            $url_parts['query'] = $this->getOption(self::OPTION_FORCE_QUERY);
        }

        if ($this->hasOption(self::OPTION_FORCE_FRAGMENT)) {
            $url_parts['fragment'] = $this->getOption(self::OPTION_FORCE_FRAGMENT);
        }

        // check for required parts according to existing options

        $require_user = $this->toBoolean($this->getOption(self::OPTION_REQUIRE_USER, false));
        if ($require_user && !array_key_exists('user', $url_parts)) {
            $this->throwError('user_part_missing', [ 'value' => $val ]);
            return false;
        }

        $require_pass = $this->toBoolean($this->getOption(self::OPTION_REQUIRE_PASS, false));
        if ($require_pass && !array_key_exists('pass', $url_parts)) {
            $this->throwError('pass_part_missing', [ 'value' => $val ]);
            return false;
        }

        $require_port = $this->toBoolean($this->getOption(self::OPTION_REQUIRE_PORT, false));
        if ($require_port && !array_key_exists('port', $url_parts)) {
            $this->throwError('port_part_missing', [ 'value' => $val ]);
            return false;
        }

        $require_path = $this->toBoolean($this->getOption(self::OPTION_REQUIRE_PATH, false));
        if ($require_path && !array_key_exists('path', $url_parts)) {
            $this->throwError('path_part_missing', [ 'value' => $val ]);
            return false;
        }

        $require_query = $this->toBoolean($this->getOption(self::OPTION_REQUIRE_QUERY, false));
        if ($require_query && !array_key_exists('query', $url_parts)) {
            $this->throwError('query_part_missing', [ 'value' => $val ]);
            return false;
        }

        $require_fragment = $this->toBoolean($this->getOption(self::OPTION_REQUIRE_FRAGMENT, false));
        if ($require_fragment && !array_key_exists('fragment', $url_parts)) {
            $this->throwError('fragment_part_missing', [ 'value' => $val ]);
            return false;
        }

        $use_idn = $this->toBoolean($this->getOption(self::OPTION_USE_IDN, true));
        $convert_host_to_punycode = $this->toBoolean($this->getOption(self::OPTION_CONVERT_HOST_TO_PUNYCODE, false));
        $idn_available = function_exists('idn_to_ascii') ? true : false;
        if (!$idn_available && $use_idn) {
            throw new RuntimeException(
                'The INTL extension needs to be installed to check international domain names of URLs.'
            );
        }
        if (!$idn_available && $convert_host_to_punycode) {
            throw new RuntimeException(
                'The INTL extension needs to be installed to convert domains names to punycode.'
            );
        }

        // test url parts are the ones used to generate a complete URL ALWAYS containing a scheme
        $test_url_parts = $url_parts;

        // punycode url parts are the ones used to generate a punycode URL ALWAYS containing a scheme
        $punycode_url_parts = $test_url_parts;

        //$ipv4_host = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

        $ipv6_host = $url_parts['host'];
        if ($ipv6_host[0] === '[' && mb_substr($ipv6_host, -1) === ']') {
            $ipv6_host = mb_substr($ipv6_host, 1, -1);
        }
        $ipv6_host = filter_var($ipv6_host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        if ($ipv6_host !== false) {
            $ipv6_host = '[' . $url_parts['host'] . ']';
            $test_url_parts['host'] = 'ipv6domain.de'; // just for filter_var test as it doesn't understand ipv6
        }

        // check host for being convertible to punycode
        if ($use_idn) {
            $idn_host = idn_to_ascii($url_parts['host']); // @TODO options, variants, idna_info
            if ($idn_host === false) {
                $this->throwError('invalid_idn_host', [ 'value' => $val ]);
                return false;
            }

            $punycode_url_parts['host'] = $idn_host;
        }

        /**
         * Check for suspicious letters in the domain name (confusable chars from other charsets, e.g. cyrillic)
         * @see http://en.wikipedia.org/wiki/IDN_homograph_attack
         * @see http://kb.mozillazine.org/Network.IDN.blacklist_chars
         * @see http://stackoverflow.com/questions/17458876/php-spoofchecker-class
         * @see http://www.unicode.org/Public/security/revision-06/confusables.txt
         * @see http://icu-project.org/apiref/icu4j50m1/com/ibm/icu/text/SpoofChecker.html for docs on constants
         */
        $accept_suspicious_host = $this->toBoolean($this->getOption(self::OPTION_ACCEPT_SUSPICIOUS_HOST, true));
        $convert_suspicious_host = $this->toBoolean($this->getOption(self::OPTION_CONVERT_SUSPICIOUS_HOST, true));
        $spoofchecker_available = extension_loaded('intl') && class_exists("Spoofchecker");
        if ((!$spoofchecker_available && $convert_suspicious_host) ||
            (!$spoofchecker_available && !$accept_suspicious_host)
        ) {
            throw new RuntimeException(
                'The INTL extension needs to be installed to spoofcheck for suspicious domains.'
            );
        }

        $is_suspicious = false;
        if ($spoofchecker_available) {
            $spoofchecker = new Spoofchecker();
            /**
             * Check whether two strings are visually confusable and:
             * - SINGLE_SCRIPT_CONFUSABLE: all of the characters from the two strings are from a single script
             * - MIXED_SCRIPT_CONFUSABLE: at least one string contains characters from more than one script
             * - WHOLE_SCRIPT_CONFUSABLE: each strings is of a single script, but they're from different scripts
             * - ANY_CASE: check case-sensitive confusability (even though domains are not)
             * - INVISIBLE: do not allow invisible characters like non-spacing marks
             */
            $checks = (int)$this->getOption(
                self::OPTION_DOMAIN_SPOOFCHECKER_CHECKS,
                Spoofchecker::SINGLE_SCRIPT_CONFUSABLE |
                Spoofchecker::MIXED_SCRIPT_CONFUSABLE |
                Spoofchecker::WHOLE_SCRIPT_CONFUSABLE |
                Spoofchecker::ANY_CASE |
                Spoofchecker::INVISIBLE
            );
            $spoofchecker->setChecks($checks);

            $is_suspicious = $spoofchecker->isSuspicious($url_parts['host'], $error);
            if ($is_suspicious && !$accept_suspicious_host) {
                $this->throwError('suspicious_domain', [ 'value' => $val, 'error' => $error ]);
                return false;
            }
            // TODO spoofcheck other parts of the url?
        }

        // generate URLs for filter_var test and setting as sanitized value
        $url = $this->getUrlFromArray($url_parts);
        $test_url = $this->getUrlFromArray($test_url_parts);
        $punycode_url = $this->getUrlFromArray($punycode_url_parts);

        $filter_flags = 0;
        if ($this->toBoolean(self::OPTION_REQUIRE_PATH, false)) {
            $filter_flags |= FILTER_FLAG_PATH_REQUIRED;
        }
        if ($this->toBoolean(self::OPTION_REQUIRE_QUERY, false)) {
            $filter_flags |= FILTER_FLAG_QUERY_REQUIRED;
        }

        if ($ipv6_host !== false) {
            $test = filter_var($test_url, FILTER_VALIDATE_URL, $filter_flags);
        } else {
            $test = filter_var($punycode_url, FILTER_VALIDATE_URL, $filter_flags);
        }

        if ($test === false) {
            $this->throwError('invalid_format', [ 'url' => $url, 'punycode_url' => $punycode_url, 'value' => $val ]);
            return false;
        }

        if ($use_idn && $convert_host_to_punycode) {
            $this->setSanitizedValue($punycode_url);
        } elseif ($is_suspicious && $convert_suspicious_host) {
            $this->setSanitizedValue($punycode_url);
        } else {
            $this->setSanitizedValue($url);
        }

        return true;

    }

    protected function getUrlFromArray(array $url_parts)
    {
        // generate the resulting URL
        $url = $url_parts['scheme'] . $this->getOption(self::OPTION_SCHEME_SEPARATOR, '://');

        // add optional auth info
        if (array_key_exists('user', $url_parts)) {
            $url .= $url_parts['user'];
        }
        if (array_key_exists('pass', $url_parts)) {
            $url .= ':' . $url_parts['pass'];
        }
        if (array_key_exists('user', $url_parts) || array_key_exists('pass', $url_parts)) {
            $url .= '@';
        }

        // add host
        $url .= $url_parts['host'];

        // add port
        if (array_key_exists('port', $url_parts)) {
            $url .= ':' . $url_parts['port'];
        }

        // add path
        if (array_key_exists('path', $url_parts)) {
            $url .= $url_parts['path'];
        }/* else {
            $url .= '/';
        }*/

        // add query part
        // TODO uri encode query parts?
        if (array_key_exists('query', $url_parts)) {
            if (!array_key_exists('path', $url_parts)) {
                $url .= '/';
            }
            $url .= '?' . $url_parts['query'];
        }

        // add fragment
        if (array_key_exists('fragment', $url_parts)) {
            if (!array_key_exists('path', $url_parts) && !array_key_exists('query', $url_parts)) {
                $url .= '/';
            }
            $url .= '#' . $url_parts['fragment'];
        }

        return $url;
    }
}
