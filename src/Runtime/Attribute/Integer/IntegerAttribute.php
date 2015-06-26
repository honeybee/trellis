<?php

namespace Trellis\Runtime\Attribute\Integer;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\IntegerRule;

class IntegerAttribute extends Attribute
{
    const OPTION_ALLOW_HEX      = IntegerRule::OPTION_ALLOW_HEX;
    const OPTION_ALLOW_OCTAL    = IntegerRule::OPTION_ALLOW_OCTAL;
    const OPTION_MIN_VALUE      = IntegerRule::OPTION_MIN_VALUE;
    const OPTION_MAX_VALUE      = IntegerRule::OPTION_MAX_VALUE;

    public function getNullValue()
    {
        $null_value = $this->getOption(self::OPTION_NULL_VALUE, 0);

        if (!$this->isInRange($null_value)) {
            $null_value = $this->getOption(
                self::OPTION_MIN_VALUE,
                $this->getOption(self::OPTION_MAX_VALUE, $null_value)
            );
        }

        return $null_value;
    }

    public function getDefaultValue()
    {
        $default_value = parent::getDefaultValue();

        if (!$this->isInRange($default_value)) {
            throw new InvalidConfigException(
                "Configured range option ('min_value' or 'max_value') not compatible with the 'default' value."
            );
        }

        return $default_value;
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new IntegerRule('valid-integer', $options));

        return $rules;
    }

    protected function isInRange($value)
    {
        if (($this->hasOption(self::OPTION_MIN_VALUE) && $value < $this->getOption(self::OPTION_MIN_VALUE)) ||
            ($this->hasOption(self::OPTION_MAX_VALUE) && $value > $this->getOption(self::OPTION_MAX_VALUE))) {
            return false;
        }

        return true;
    }
}
