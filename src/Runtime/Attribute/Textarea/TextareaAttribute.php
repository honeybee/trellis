<?php

namespace Trellis\Runtime\Attribute\Textarea;

use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\TextRule;

/**
 * Like single line texts by default, but allows TAB and NEWLINE characters.
 */
class TextareaAttribute extends TextAttribute
{
    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        if (!array_key_exists(self::OPTION_ALLOW_CRLF, $options)) {
            $options[self::OPTION_ALLOW_CRLF] = true;
        }

        if (!array_key_exists(self::OPTION_ALLOW_TAB, $options)) {
            $options[self::OPTION_ALLOW_TAB] = true;
        }

        $rules->push(new TextRule('valid-text', $options));

        return $rules;
    }
}
