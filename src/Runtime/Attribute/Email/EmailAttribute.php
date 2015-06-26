<?php

namespace Trellis\Runtime\Attribute\Email;

use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\EmailRule;

class EmailAttribute extends TextAttribute
{
    protected function buildValidationRules()
    {
        $rules = new RuleList();
        $rules->push(new EmailRule('email-type', $this->getOptions()));
        return $rules;
    }
}
