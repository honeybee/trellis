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
        return null;
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
        if ($this->hasOption(self::OPTION_DEFAULT_VALUE)) {
            if ($this->getOption(self::OPTION_DEFAULT_VALUE) === 'auto_gen') {
                $max_length = $this->getOption(self::OPTION_MAX_LENGTH, 40);
                $token = bin2hex(mcrypt_create_iv(ceil($max_length/2), MCRYPT_DEV_URANDOM));
                $raw_default = substr($token, 0, $max_length);
            } else {
                $raw_default = $this->getOption(self::OPTION_DEFAULT_VALUE, $this->getNullValue());
            }
            return $this->getSanitizedValue($raw_default);
        } else {
            return $this->getNullValue();
        }
    }
}
