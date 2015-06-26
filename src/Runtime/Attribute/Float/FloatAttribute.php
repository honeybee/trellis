<?php

namespace Trellis\Runtime\Attribute\Float;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\FloatRule;

class FloatAttribute extends Attribute
{
    /**
     * Allow fraction separator (',' as in '1,200' === 1200)
     */
    const OPTION_ALLOW_THOUSAND_SEPARATOR = FloatRule::OPTION_ALLOW_THOUSAND_SEPARATOR;

    /**
     * precision when comparing two float values for equality. Falls back
     * to the php ini setting 'precision' (usually 14).
     */
    const OPTION_PRECISION_DIGITS = FloatRule::OPTION_PRECISION_DIGITS;

    /**
     * Whether of not to accept infinite float values. Please note, that
     * the toNative representation of infinite values is a special string
     * that is known by the validation rule to set infinity as the internal
     * value on reconstitution. This string is most likely neither valid nor
     * acceptable in other representation formats that are created upon the
     * toNative representation (e.g. json_encode and reading that value via
     * javascript and through sorcery hope that it's a float).
     */
    const OPTION_ALLOW_INFINITY = FloatRule::OPTION_ALLOW_INFINITY;

    /**
     * Whether of not to accept NAN float values. Please note, that
     * the toNative representation of not-a-number values is a special string
     * that is known by the validation rule to set NAN as the internal
     * value on reconstitution. This string is most likely neither valid nor
     * acceptable in other representation formats that are created upon the
     * toNative representation (e.g. json_encode and reading that value via
     * javascript and through sorcery hope that it's a float).
     */
    const OPTION_ALLOW_NAN = FloatRule::OPTION_ALLOW_NAN;

    const OPTION_MAX_VALUE = FloatRule::OPTION_MAX_VALUE;
    const OPTION_MIN_VALUE = FloatRule::OPTION_MIN_VALUE;

    public function getNullValue()
    {
        $null_value = $this->getOption(self::OPTION_NULL_VALUE, 0.0);

        if (!$this->isInRange($null_value)) {
            $null_value = (float)$this->getOption(
                self::OPTION_MIN_VALUE,
                (float)$this->getOption(self::OPTION_MAX_VALUE, (float)$null_value)
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

        $rules->push(new FloatRule('valid-float', $options));

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
