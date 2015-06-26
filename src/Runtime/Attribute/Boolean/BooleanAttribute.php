<?php

namespace Trellis\Runtime\Attribute\Boolean;

use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\BooleanRule;

class BooleanAttribute extends Attribute
{
    public function getNullValue()
    {
        $value = $this->getOption(self::OPTION_NULL_VALUE, false);

        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $bool) {
            return false;
        }

        return $bool;
    }

    public function getDefaultValue()
    {
        $value = $this->getOption(self::OPTION_DEFAULT_VALUE, false);

        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $bool) {
            return $this->getNullValue();
        }

        return $bool;
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $rules->push(new BooleanRule('valid-boolean'));

        return $rules;
    }
}
