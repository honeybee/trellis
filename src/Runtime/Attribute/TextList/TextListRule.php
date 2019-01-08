<?php

namespace Trellis\Runtime\Attribute\TextList;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;

class TextListRule extends Rule
{
    const OPTION_ALLOWED_VALUES             = 'allowed_values';

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

    protected function execute($values, EntityInterface $entity = null)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $allowed_values = [];
        if ($this->hasOption(self::OPTION_ALLOWED_VALUES)) {
            $allowed_values = $this->getAllowedValues();
        }

        $sanitized = [];

        $text_rule = new TextRule('text', $this->getOptions());

        foreach ($values as $val) {
            $is_valid = $text_rule->apply($val);
            if (!$is_valid) {
                foreach ($text_rule->getIncidents() as $incident) {
                    $this->throwError($incident->getName(), $incident->getParameters(), $incident->getSeverity());
                }
                return false;
            } else {
                $val = $text_rule->getSanitizedValue();
            }

            // check for allowed values
            if ($this->hasOption(self::OPTION_ALLOWED_VALUES)) {
                if (!in_array($val, $allowed_values, true)) {
                    $this->throwError(
                        self::OPTION_ALLOWED_VALUES,
                        [
                            self::OPTION_ALLOWED_VALUES => $allowed_values,
                            'value' => $val
                        ]
                    );
                    return false;
                }
            }

            $sanitized[] = $val;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }

    protected function getAllowedValues()
    {
        $allowed_values = [];

        $configured_allowed_values = $this->getOption(self::OPTION_ALLOWED_VALUES, []);
        if (!is_array($allowed_values)) {
            throw new InvalidConfigException('Configured allowed_values must be an array of permitted values.');
        }

        foreach ($configured_allowed_values as $key => $raw) {
            $allowed_values[$key] = (string)$raw;
        }

        return $allowed_values;
    }
}
