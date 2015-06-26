<?php

namespace Trellis\Runtime\Attribute\IntegerList;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Attribute\ListAttribute;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\RuleList;

class IntegerListAttribute extends ListAttribute
{
    const OPTION_ALLOW_HEX      = IntegerListRule::OPTION_ALLOW_HEX;
    const OPTION_ALLOW_OCTAL    = IntegerListRule::OPTION_ALLOW_OCTAL;
    const OPTION_MIN_VALUE      = IntegerListRule::OPTION_MIN_VALUE;
    const OPTION_MAX_VALUE      = IntegerListRule::OPTION_MAX_VALUE;

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new IntegerListRule('valid-integer-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
