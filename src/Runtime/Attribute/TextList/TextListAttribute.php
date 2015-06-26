<?php

namespace Trellis\Runtime\Attribute\TextList;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Attribute\ListAttribute;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\TextRule;

/**
 * A list of strings.
 */
class TextListAttribute extends ListAttribute
{
    const OPTION_ALLOWED_VALUES             = TextListRule::OPTION_ALLOWED_VALUES;

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

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new TextListRule('valid-text-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
