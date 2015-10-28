<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;

/**
 * While fully portable POSIX filenames can only contain "A-Za-z0-9._-" this
 * sanitizing validation rule only tries to sanitize various special or
 * invalid characters. The returned value should thus be further stripped
 * down according to expected filesystems etc. instead of using the string
 * directly as a filename. DON'T USE THE RESULT DIRECTLY AS A FILENAME.
 *
 * Accepts strings and:
 * - strips \x00 and invalid utf8 sequences
 * - trims the string
 * - removes control characters (including TAB/CR/LF)
 * - strips zero-width space
 * - strips LTR/RTL text direction override characters
 * - strips zero-width joiner and zero-width non-joiner at the end of the text
 * - resolve relative paths to get nicer filenames when replacing chars later on
 * - replace multiple occurrences of '.' like '...' with one '.'
 * - replaces special characters that may be problematic in shells etc with '-' ('/', '|'…)
 * - optionally lowercases the result to prevent problems w/ case-insensitive filesystems
 *
 * Minimum and maximum string length check AFTER trimming is possible.
 */
class SanitizedFilenameRule extends Rule
{
    const OPTION_MAX_LENGTH = 'max_length';
    const OPTION_MIN_LENGTH = 'min_length';

    const OPTION_REPLACE_SPECIAL_CHARS = 'replace_special_chars';
    const OPTION_REPLACE_WITH = 'replace_with';

    const OPTION_LOWERCASE = 'lowercase';
    const OPTION_SPOOFCHECK_RESULT = 'spoofcheck_result';

    protected function execute($value, EntityInterface $entity = null)
    {
        if (!is_string($value)) {
            $this->throwError('non_string_value', [ 'value' => $value ], IncidentInterface::CRITICAL);
            return false;
        }

        // @see http://hakipedia.com/index.php/Poison_Null_Byte
        $value = str_replace(chr(0), '', $value);

        // remove zero-width space character from text
        $value = str_replace("\xE2\x80\x8B", '', $value);

        // strip unicode characters 'RIGHT-TO-LEFT OVERRIDE' and 'LEFT-TO-RIGHT OVERRIDE'
        // which can be used to turn 'image[RTLO]gpj.exe' into 'imageexe.jpg'
        $value = str_replace("\xE2\x80\xAE", '', $value); // 'RIGHT-TO-LEFT OVERRIDE'
        $value = str_replace("\xE2\x80\xAD", '', $value); // 'LEFT-TO-RIGHT OVERRIDE'

        /**
         * Some links for illformed byte sequences etc.:
         *
         * @see http://php.net/manual/de/function.mb-check-encoding.php
         * @see http://www.w3.org/International/questions/qa-forms-utf-8.en.php
         * @see http://unicode.org/reports/tr36/#Ill-Formed_Subsequences
         * @see http://www.cl.cam.ac.uk/~mgk25/ucs/examples/UTF-8-test.txt
         */

        // strip invalid utf8 characters
        // use mbstring here instead of iconv with '//ignore' – https://bugs.php.net/bug.php?id=61484
        // $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
        $prev = ini_set('mbstring.substitute_character', 'none');
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        ini_set('mbstring.substitute_character', $prev);

        // trim the input string
        // note: '/(*UTF8)[[:alnum:]]/' matches 'é' while '/[[:alnum:]]/' does not
        $pattern = '/(*UTF8)^[\pZ\pC]*+(?P<trimmed>.*?)[\pZ\pC]*+$/usDS';
        if (preg_match($pattern, $value, $matches)) {
            $value = $matches['trimmed'];
        }

        // trim zero-width joiner and zero-width non-joiner (at the end of the text)
        // https://en.wikipedia.org/wiki/Zero-width_non-joiner
        $value = preg_replace("/\xE2\x80\x8C$/", '', $value); // zero-width non-joiner
        $value = preg_replace("/\xE2\x80\x8D$/", '', $value); // zero-width joiner

        // æ is a ligature in english but a distinct letter in icelandic and other languages
        // additionally remove some control characters
        // remove non-printable control characters including TAB, LINE FEED, CARRIAGE RETURN
        // $remove_pattern = "/[\x01-\x08\x09\x0A\x0B\x0C\x0D\x0E-\x1F\x7F]/u";
        $remove_chars = [
            "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08", "\x09",
            "\x0A", "\x0B", "\x0C", "\x0D", "\x0E", "\x0F", "\x10", "\x11", "\x12",
            "\x13", "\x14", "\x15", "\x16", "\x17", "\x18", "\x19", "\x1A", "\x1B",
            "\x1C", "\x1D", "\x1E", "\x1F", "\x7F",
            "\n", "\r\n", "\r",
        ];

        $value = str_replace($remove_chars, '', $value);
        if (!is_string($value)) {
            $this->throwError('control_character_stripping_failed', [ ], IncidentInterface::CRITICAL);
            return false;
        }

        // solve relative paths like 'folder/../file.ext' – as we probably replace the '/' with '-'
        // anyways later on this might seem unnecessary, but leads to nicer filenames with less '-'
        do {
            $value = preg_replace('#[^/\.]+/\.\./#', '', $value, -1, $count);
        } while ($count);
        $value = str_replace(['/./', '//'], '/', $value);

        // replace multiple occurrences of '.' with one '.'
        $value = preg_replace('/\.{2,}/', '.', $value);

        $replace_special_chars = $this->toBoolean($this->getOption(self::OPTION_REPLACE_SPECIAL_CHARS, true));
        if ($replace_special_chars) {
            $replace_chars = [
                '#', '<', '$', '+', '%', '>', '!', '`', '&', '*', '‘',
                '|', '{', '?', '“', '=', '}', '/', ':', '\\', ' ', '@'
            ];
            $replace_with = $this->getOption(self::OPTION_REPLACE_WITH, '-');

            $value = str_replace($replace_chars, $replace_with, $value);
            if (!is_string($value)) {
                $this->throwError('character_replacing_failed', [ ], IncidentInterface::CRITICAL);
                return false;
            }
        }

        // trim '.' and '-' (so regardless of LTR or RTL script the filename doesn't start
        // with a dot to prevent generating a hidden dotfile filename
        $value = trim($value, '.-');

        // check minimum string length
        if ($this->hasOption(self::OPTION_MIN_LENGTH)) {
            $min = filter_var($this->getOption(self::OPTION_MIN_LENGTH, -PHP_INT_MAX-1), FILTER_VALIDATE_INT);
            if ($min === false) {
                throw new InvalidConfigException('Minimum string length specified is not interpretable as integer.');
            }
            if (mb_strlen($value) < $min) {
                $this->throwError(
                    self::OPTION_MIN_LENGTH,
                    [ self::OPTION_MIN_LENGTH => $min, 'value' => $value ]
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
            if (mb_strlen($value) > $max) {
                $this->throwError(
                    self::OPTION_MAX_LENGTH,
                    [ self::OPTION_MAX_LENGTH => $max, 'value' => $value ]
                );
                return false;
            }
        }

        $lowercase = $this->toBoolean($this->getOption(self::OPTION_LOWERCASE, false));
        if ($lowercase) {
            $value = mb_strtolower($value, 'UTF-8');
            // TODO it's probably advisable to manually lowercase some more variants as mentioned
            // in this comment: http://php.net/manual/de/function.mb-strtolower.php#105753
            //$value = strtr($value, $additional_replacements);
        }

        $spoofcheck_resulting_value = $this->toBoolean($this->getOption(self::OPTION_SPOOFCHECK_RESULT, false));
        if ($spoofcheck_resulting_value) {
            $rule = new SpoofcheckerRule('spoofcheck-resulting-text', $this->getOptions());
            if (!$rule->apply($value)) {
                foreach ($rule->getIncidents() as $incident) {
                    $this->throwError($incident->getName(), $incident->getParameters(), $incident->getSeverity());
                }
                return false;
            } else {
                $value = $rule->getSanitizedValue();
            }
        }

        $this->setSanitizedValue($value);

        return true;
    }
}
