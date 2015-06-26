<?php

namespace Trellis\Runtime\Attribute\BooleanList;

use Trellis\Runtime\Attribute\ListAttribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\ListRule;

class BooleanListAttribute extends ListAttribute
{
    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rules->push(new ListRule('valid-list', $options));
        $rules->push(new BooleanListRule('valid-boolean-list', $options));

        return $rules;
    }
}
