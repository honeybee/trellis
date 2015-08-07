<?php

namespace Trellis\Runtime\Attribute\Token;

use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\TokenRule;

class TokenAttribute extends Attribute
{
    const OPTION_MAX_LENGTH                 = TokenRule::OPTION_MAX_LENGTH;
    const OPTION_MIN_LENGTH                 = TokenRule::OPTION_MIN_LENGTH;

    public function getNullValue()
    {
        return '';
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(
            new TokenRule('valid-token', $options)
        );

        return $rules;
    }

    public function getDefaultValue()
    {
        return $this->getNullValue();
    }
}
