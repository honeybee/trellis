<?php

namespace Trellis\Runtime\Attribute\Text;

use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\TextRule;

/**
 * Allows valid UTF8 texts, trims it, strips control characters except tabs and
 * newlines and spoofchecks the incoming and/or resulting text if wanted.
 * For valid options see TextRule and SpoofcheckerRule.
 */
class TextAttribute extends Attribute
{
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

    public function getNullValue()
    {
        return '';
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $rules->push(new TextRule('valid-text', $this->getOptions()));

        return $rules;
    }
}
