<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Common\Error\RuntimeException;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Spoofchecker;
use Trellis\Runtime\Entity\EntityInterface;

/**
 * Check a string for suspicious letters. This may include confusable
 * characters from different scripts, e.g. cyrillic characters in german text.
 *
 * It's possible to check whether the given string is visually confusable with
 * a configured list of strings.
 *
 * Further on it's possible to reject a string when it contaings characters
 * that are usually invisble (zero-width space…).
 *
 * It's possible to allow only certain locales or enforce single (non-mixed)
 * scripts.
 *
 * @see http://en.wikipedia.org/wiki/IDN_homograph_attack
 * @see http://kb.mozillazine.org/Network.IDN.blacklist_chars
 * @see http://stackoverflow.com/questions/17458876/php-spoofchecker-class
 * @see http://www.unicode.org/Public/security/revision-06/confusables.txt
 * @see http://icu-project.org/apiref/icu4j50m1/com/ibm/icu/text/SpoofChecker.html for docs on constants
 */
class SpoofcheckerRule extends Rule
{
    const OPTION_ACCEPT_SUSPICIOUS_STRINGS = 'accept_suspicious_strings';
    const OPTION_ALLOWED_LOCALES = 'allowed_locales';
    const OPTION_ANY_CASE_CONFUSABLE = 'any_case_confusable';
    const OPTION_ENFORCE_SINGLE_SCRIPT = 'enforce_single_script';
    const OPTION_IGNORE_MISSING_CLASS = 'ignore_missing_class';
    const OPTION_REJECT_INVISIBLE_CHARACTERS = 'reject_invisible_characters';
    const OPTION_VISUALLY_CONFUSABLE_STRINGS = 'visually_confusable_strings';

    protected function execute($value, EntityInterface $entity = null)
    {
        if (!is_string($value)) {
            $this->throwError('non_string_value', [ 'value' => $value ], IncidentInterface::CRITICAL);
            return false;
        }

        $ignore_missing_class = $this->getOption(self::OPTION_IGNORE_MISSING_CLASS, false);
        $spoofchecker_available = extension_loaded('intl') && class_exists("Spoofchecker");
        if (!$spoofchecker_available) {
            if ($ignore_missing_class) {
                return true; // silently assume a valid string
            }

            throw new RuntimeException(
                'The INTL extension needs to be installed to spoofcheck for suspicious domains.'
            );
        }

        // check if a string is suspicious, e.g. containing multiple scripts
        $is_suspicious = false;
        $accept_suspicious_strings = $this->getOption(self::OPTION_ACCEPT_SUSPICIOUS_STRINGS, false);
        if (!$accept_suspicious_strings) {
            $spoofchecker = $this->getSuspiciousChecker();
            $error = '';
            $is_suspicious = $spoofchecker->isSuspicious($value, $error);
            if ($is_suspicious) {
                //var_dump("$value => $error");
                $this->throwError('suspicious_string', [ 'value' => $value, 'error' => $error ]);
                return false;
            }
        }

        // compare if string is visually confusable with a configured list of strings
        $visually_confusable_strings = $this->getOption(self::OPTION_VISUALLY_CONFUSABLE_STRINGS, []);
        if (is_string($visually_confusable_strings)) {
            $visually_confusable_strings = [ $visually_confusable_strings ];
        }
        if ($this->hasOption(self::OPTION_VISUALLY_CONFUSABLE_STRINGS)) {
            $spoofchecker = $this->getConfusableChecker();
            foreach ($visually_confusable_strings as $text) {
                $error = '';
                if ($spoofchecker->areConfusable($value, $text, $error)) {
                    $this->throwError(
                        'visually_confusable',
                        [
                            'value' => $value,
                            'confusable_with' => $text,
                            'error' => $error
                        ]
                    );
                    return false;
                }
            }
        }

        // \o/, string seems to be neither suspicious nor confusable
        $this->setSanitizedValue($value);

        return true;
    }

    protected function getSuspiciousChecker()
    {
        $spoofchecker = new Spoofchecker();

        // Allowing only certain locales enables rejection of e.g. korean script when a text should be en_US only
        $allowed_locales = $this->getOption(self::OPTION_ALLOWED_LOCALES, []);
        if ($this->hasOption(self::OPTION_ALLOWED_LOCALES)) {
            if (is_array($allowed_locales)) {
                $allowed_locales = implode(', ', $allowed_locales);
            }
            if (!is_string($allowed_locales)) {
                throw new InvalidConfigException(
                    'Given allowed locales must be a comma separated string of array of strings. ' .
                    'Use a string like "de_DE, en_US" or an array of such locale identifiers.'
                );
            }
            $spoofchecker->setAllowedLocales($allowed_locales);
        }

        /**
         * Check whether two strings are visually confusable and:
         * - SINGLE_SCRIPT_CONFUSABLE: all of the characters from the two strings are from a single script
         * - MIXED_SCRIPT_CONFUSABLE: at least one of the strings contains characters from more than one script
         */
        $checks = Spoofchecker::SINGLE_SCRIPT_CONFUSABLE | Spoofchecker::MIXED_SCRIPT_CONFUSABLE;

        // find confusable characters of any case?
        $any_case_confusable = $this->getOption(self::OPTION_ANY_CASE_CONFUSABLE, true);
        if ($any_case_confusable) {
            /**
             * ANY_CASE is a modifier for the *_SCRIPT_CONFUSABLE checks.
             * DO specify if:
             * - the strings being checked can be of mixed case and
             * - are used in a case-sensitive manner
             * DON'T specify if:
             * - the strings being checked are used in a case-insensitive manner, and
             * - if they are displayed to users in lower-case form only
             */
            $checks |= Spoofchecker::ANY_CASE;
        }

        // reject zero-width space, non-spacing mark etc.
        $reject_invisible_characters = $this->getOption(self::OPTION_REJECT_INVISIBLE_CHARACTERS, true);
        if ($reject_invisible_characters) {
            /**
             * Do not allow the presence of invisible characters, such as zero-width spaces, or character sequences
             * that are likely not to display, such as multiple occurrences of the same non-spacing mark.
             */
            $checks |= Spoofchecker::INVISIBLE;
        }

        // enforce a single script instead of allowing multiple when they're not confusable/suspicious?
        $enforce_single_script = $this->getOption(self::OPTION_ENFORCE_SINGLE_SCRIPT, false);
        if ($enforce_single_script) {
            /**
             * Single-script enforcement is turned off in the Spoofchecker class by default as it fails
             * with languages like Japanese that legally use multiple scripts within single words.
             */
            $checks |= Spoofchecker::SINGLE_SCRIPT;
        }

        $spoofchecker->setChecks($checks);

        return $spoofchecker;
    }

    protected function getConfusableChecker()
    {
        $spoofchecker = new Spoofchecker();

        // Allowing only certain locales enables rejection of e.g. korean script when a text should be en_US only
        $allowed_locales = $this->getOption(self::OPTION_ALLOWED_LOCALES, []);
        if ($this->hasOption(self::OPTION_ALLOWED_LOCALES)) {
            if (is_array($allowed_locales)) {
                $allowed_locales = implode(', ', $allowed_locales);
            }
            if (!is_string($allowed_locales)) {
                throw new InvalidConfigException(
                    'Given allowed locales must be a comma separated string of array of strings. ' .
                    'Use a string like "de_DE, en_US" or an array of such locale identifiers.'
                );
            }
            $spoofchecker->setAllowedLocales($allowed_locales);
        }

        /**
         * Check whether two strings are visually confusable and:
         * - SINGLE_SCRIPT_CONFUSABLE: all of the characters from the two strings are from a single script
         * - MIXED_SCRIPT_CONFUSABLE: at least one of the strings contains characters from more than one script
         * - WHOLE_SCRIPT_CONFUSABLE: each of the two strings is of a single script, but they're from different scripts
         */
        $checks = Spoofchecker::SINGLE_SCRIPT_CONFUSABLE |
            Spoofchecker::MIXED_SCRIPT_CONFUSABLE |
            Spoofchecker::WHOLE_SCRIPT_CONFUSABLE;

        // find confusable characters of any case?
        $any_case_confusable = $this->getOption(self::OPTION_ANY_CASE_CONFUSABLE, true);
        if ($any_case_confusable) {
            /**
             * ANY_CASE is a modifier for the *_SCRIPT_CONFUSABLE checks.
             * DO specify if:
             * - the strings being checked can be of mixed case and
             * - are used in a case-sensitive manner
             * DON'T specify if:
             * - the strings being checked are used in a case-insensitive manner, and
             * - if they are displayed to users in lower-case form only
             */
            $checks |= Spoofchecker::ANY_CASE;
        }

        // reject zero-width space, non-spacing mark etc.? those characters are suspicious anyways…
        $reject_invisible_characters = $this->getOption(self::OPTION_REJECT_INVISIBLE_CHARACTERS, true);
        if ($reject_invisible_characters) {
            /**
             * Do not allow the presence of invisible characters, such as zero-width spaces, or character sequences
             * that are likely not to display, such as multiple occurrences of the same non-spacing mark.
             */
            $checks |= Spoofchecker::INVISIBLE;
        }

        // enforce a single script instead of allowing multiple when they're not confusable/suspicious?
        $enforce_single_script = $this->getOption(self::OPTION_ENFORCE_SINGLE_SCRIPT, false);
        if ($enforce_single_script) {
            /**
             * Single-script enforcement is turned off in the Spoofchecker class by default as it fails
             * with languages like Japanese that legally use multiple scripts within single words.
             */
            $checks |= Spoofchecker::SINGLE_SCRIPT;
        }

        $spoofchecker->setChecks($checks);

        return $spoofchecker;
    }
}
