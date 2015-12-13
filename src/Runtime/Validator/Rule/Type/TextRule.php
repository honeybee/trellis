<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;

/**
 * Accepts strings and:
 * - only accepts valid utf8
 * - strips \x00 and invalid utf8 sequences
 * - trims the string
 * - removes certain control characters (including TAB/CR/LF by default)
 * - optionally normalizes new line characters to \n
 * - optionally strips zero-width space
 * - optionally strips LTR/RTL text direction override characters
 *
 * Minimum and maximum string length check AFTER trimming is possible.
 */
class TextRule extends Rule
{
    const OPTION_ALLOW_CRLF = 'allow_crlf';
    const OPTION_ALLOW_TAB = 'allow_tab';
    const OPTION_MAX_LENGTH = 'max_length';
    const OPTION_MIN_LENGTH = 'min_length';
    const OPTION_NORMALIZE_NEWLINES = 'normalize_newlines';
    const OPTION_REJECT_INVALID_UTF8 = 'reject_invalid_utf8';
    const OPTION_STRIP_CONTROL_CHARACTERS = 'strip_control_characters';
    const OPTION_STRIP_DIRECTION_OVERRIDES = 'strip_direction_overrides';
    const OPTION_STRIP_INVALID_UTF8 = 'strip_invalid_utf8';
    const OPTION_STRIP_NULL_BYTES = 'strip_null_bytes';
    const OPTION_STRIP_ZERO_WIDTH_SPACE = 'strip_zero_width_space';
    const OPTION_TRIM = 'trim';

    const OPTION_SPOOFCHECK_INCOMING = 'spoofcheck_incoming';
    const OPTION_SPOOFCHECK_RESULT = 'spoofcheck_result';

    protected function execute($value, EntityInterface $entity = null)
    {
        if (!is_string($value)) {
            $this->throwError('non_string_value', [ 'value' => $value ], IncidentInterface::CRITICAL);
            return false;
        }

        $spoofcheck_incoming_value = $this->getOption(self::OPTION_SPOOFCHECK_INCOMING, false);
        if ($spoofcheck_incoming_value) {
            $rule = new SpoofcheckerRule('spoofcheck-incoming-text', $this->getOptions());
            if (!$rule->apply($value)) {
                foreach ($rule->getIncidents() as $incident) {
                    $this->throwError($incident->getName(), $incident->getParameters(), $incident->getSeverity());
                }
                return false;
            } else {
                $value = $rule->getSanitizedValue();
            }
        }

        // @see http://hakipedia.com/index.php/Poison_Null_Byte
        $strip_null_bytes = $this->getOption(self::OPTION_STRIP_NULL_BYTES, true);
        if ($strip_null_bytes) {
            $value = str_replace(chr(0), '', $value);
        }

        // remove zero-width space character from text
        $strip_zero_width_space = $this->getOption(self::OPTION_STRIP_ZERO_WIDTH_SPACE, false);
        if ($strip_zero_width_space) {
            $value = str_replace("\xE2\x80\x8B", '', $value);
        }

        // strip unicode characters 'RIGHT-TO-LEFT OVERRIDE' and 'LEFT-TO-RIGHT OVERRIDE' if necessary
        $strip_direction_overrides = $this->getOption(self::OPTION_STRIP_DIRECTION_OVERRIDES, false);
        if ($strip_direction_overrides) {
            $value = str_replace("\xE2\x80\xAE", '', $value); // 'RIGHT-TO-LEFT OVERRIDE'
            $value = str_replace("\xE2\x80\xAD", '', $value); // 'LEFT-TO-RIGHT OVERRIDE'
        }

        // TODO should one allow trimming of zero-width non-joiner (only at the end of text)?

        /**
         * Some links for illformed byte sequences etc.:
         *
         * @see http://php.net/manual/de/function.mb-check-encoding.php
         * @see http://www.w3.org/International/questions/qa-forms-utf-8.en.php
         * @see http://unicode.org/reports/tr36/#Ill-Formed_Subsequences
         * @see http://www.cl.cam.ac.uk/~mgk25/ucs/examples/UTF-8-test.txt
         */

        // check for a valid utf8 string without certain byte sequences
        $reject_invalid_utf8 = $this->getOption(self::OPTION_REJECT_INVALID_UTF8, true);
        if ($reject_invalid_utf8) {
            if (!mb_check_encoding($value, 'UTF-8')) {
                $this->throwError(
                    'invalid_utf8',
                    [
                        'value' => $value,
                        'converted_value' => mb_convert_encoding($value, 'UTF-8', 'UTF-8')
                    ],
                    IncidentInterface::CRITICAL
                );
                return false;
            }
        }

        // strip invalid utf8 characters
        $strip_invalid_utf8 = $this->getOption(self::OPTION_STRIP_INVALID_UTF8, true);
        if ($strip_invalid_utf8) {
            // use mbstring here instead of iconv with '//ignore' – https://bugs.php.net/bug.php?id=61484
            // $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
            $prev = ini_set('mbstring.substitute_character', 'none');
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            ini_set('mbstring.substitute_character', $prev);
        }

        // trim the input string if necessary
        if ($this->getOption(self::OPTION_TRIM, true)) {
            //$value = trim($value);
            // note: '/(*UTF8)[[:alnum:]]/' matches 'é' while '/[[:alnum:]]/' does not
            $pattern = '/(*UTF8)^[\pZ\pC]*+(?P<trimmed>.*?)[\pZ\pC]*+$/usDS';
            if (preg_match($pattern, $value, $matches)) {
                $value = $matches['trimmed'];
            }
        }

        $sanitized_value = $value;

        // additionally remove some control characters
        $strip_ctrl_chars = $this->getOption(self::OPTION_STRIP_CONTROL_CHARACTERS, true);
        if ($strip_ctrl_chars) {
            // remove non-printable control characters, but MAYBE allow TAB, LINE FEED, CARRIAGE RETURN
            // $remove_pattern = "/[\x01-\x08\x09\x0A\x0B\x0C\x0D\x0E-\x1F\x7F]/u";
            $remove_chars = [
                "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08", "\x09",
                "\x0A", "\x0B", "\x0C", "\x0D", "\x0E", "\x0F", "\x10", "\x11", "\x12",
                "\x13", "\x14", "\x15", "\x16", "\x17", "\x18", "\x19", "\x1A", "\x1B",
                "\x1C", "\x1D", "\x1E", "\x1F", "\x7F"
            ];

            $allow_tab = $this->getOption(self::OPTION_ALLOW_TAB, true);
            if ($allow_tab) {
                unset($remove_chars[8]); // "\x09"
            }

            $allow_crlf = $this->getOption(self::OPTION_ALLOW_CRLF, false);
            if ($allow_crlf) {
                unset($remove_chars[9]); // "\x0A"
                unset($remove_chars[12]); // "\x0D"
            }

            $sanitized_value = str_replace($remove_chars, '', $value);
            if (!is_string($sanitized_value)) {
                $this->throwError('control_character_stripping_failed', [ ], IncidentInterface::CRITICAL);
                return false;
            }
        }

        $normalize_newlines = $this->getOption(self::OPTION_NORMALIZE_NEWLINES, false);
        if ($normalize_newlines) {
            $sanitized_value = str_replace(["\r\n", "\r"], "\n", $sanitized_value);
            if (!is_string($sanitized_value)) {
                $this->throwError('normalizing_newlines_failed', [ ], IncidentInterface::CRITICAL);
                return false;
            }
        }

        // check minimum string length
        if ($this->hasOption(self::OPTION_MIN_LENGTH)) {
            $min = filter_var($this->getOption(self::OPTION_MIN_LENGTH, -PHP_INT_MAX-1), FILTER_VALIDATE_INT);
            if ($min === false) {
                throw new InvalidConfigException('Minimum string length specified is not interpretable as integer.');
            }
            if (mb_strlen($sanitized_value) < $min) {
                $this->throwError(
                    self::OPTION_MIN_LENGTH,
                    [ self::OPTION_MIN_LENGTH => $min, 'value' => $sanitized_value ]
                );
                return false;
            }
        }

        // check maximum string length
        if ($this->hasOption(self::OPTION_MAX_LENGTH)) {
            $max = filter_var($this->getOption(self::OPTION_MAX_LENGTH, PHP_INT_MAX), FILTER_VALIDATE_INT);
            if ($max === false) {
                throw new InvalidConfigException('Maximum string length specified is not interpretable as integer.');
            }
            if (mb_strlen($sanitized_value) > $max) {
                $this->throwError(
                    self::OPTION_MAX_LENGTH,
                    [ self::OPTION_MAX_LENGTH => $max, 'value' => $sanitized_value ]
                );
                return false;
            }
        }

        $spoofcheck_resulting_value = $this->getOption(self::OPTION_SPOOFCHECK_RESULT, false);
        if ($spoofcheck_resulting_value) {
            $rule = new SpoofcheckerRule('spoofcheck-resulting-text', $this->getOptions());
            if (!$rule->apply($sanitized_value)) {
                foreach ($rule->getIncidents() as $incident) {
                    $this->throwError($incident->getName(), $incident->getParameters(), $incident->getSeverity());
                }
                return false;
            } else {
                $sanitized_value = $rule->getSanitizedValue();
            }
        }

        $this->setSanitizedValue($sanitized_value);

        return true;
    }
}
