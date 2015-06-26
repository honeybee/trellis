<?php

namespace Trellis\Runtime\Attribute\Choice;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Validator\Rule\Type\ChoiceRule;

/**
 * A string out of a list of allowed strings.
 */
class ChoiceAttribute extends TextAttribute
{
    const OPTION_ALLOWED_VALUES             = ChoiceRule::OPTION_ALLOWED_VALUES;

    const OPTION_ALLOW_CRLF                 = ChoiceRule::OPTION_ALLOW_CRLF;
    const OPTION_ALLOW_TAB                  = ChoiceRule::OPTION_ALLOW_TAB;
    const OPTION_MAX_LENGTH                 = ChoiceRule::OPTION_MAX_LENGTH;
    const OPTION_MIN_LENGTH                 = ChoiceRule::OPTION_MIN_LENGTH;
    const OPTION_NORMALIZE_NEWLINES         = ChoiceRule::OPTION_NORMALIZE_NEWLINES;
    const OPTION_REJECT_INVALID_UTF8        = ChoiceRule::OPTION_REJECT_INVALID_UTF8;
    const OPTION_STRIP_CONTROL_CHARACTERS   = ChoiceRule::OPTION_STRIP_CONTROL_CHARACTERS;
    const OPTION_STRIP_DIRECTION_OVERRIDES  = ChoiceRule::OPTION_STRIP_DIRECTION_OVERRIDES;
    const OPTION_STRIP_INVALID_UTF8         = ChoiceRule::OPTION_STRIP_INVALID_UTF8;
    const OPTION_STRIP_NULL_BYTES           = ChoiceRule::OPTION_STRIP_NULL_BYTES;
    const OPTION_STRIP_ZERO_WIDTH_SPACE     = ChoiceRule::OPTION_STRIP_ZERO_WIDTH_SPACE;
    const OPTION_TRIM                       = ChoiceRule::OPTION_TRIM;

    public function getNullValue()
    {
        return '';
    }

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rules->push(new ChoiceRule('valid-text', $this->getOptions()));

        return $rules;
    }
}
