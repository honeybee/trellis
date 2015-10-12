<?php

namespace Trellis\Runtime\Attribute\Uuid;

use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\UuidRule;
use Ramsey\Uuid\Uuid as UuidGenerator;

class UuidAttribute extends TextAttribute
{
    const OPTION_TRIM = UuidRule::OPTION_TRIM;

    public static function generateVersion4()
    {
        return UuidGenerator::uuid4()->toString();
    }

    public function getNullValue()
    {
        return null;
    }

    public function getDefaultValue()
    {
        if ($this->hasOption(self::OPTION_DEFAULT_VALUE)) {
            if ($this->getOption(self::OPTION_DEFAULT_VALUE) === 'auto_gen') {
                $raw_default = $this->generateVersion4();
            } else {
                $raw_default = $this->getOption(self::OPTION_DEFAULT_VALUE, $this->getNullValue());
            }
            return $this->getSanitizedValue($raw_default);
        } else {
            return $this->getNullValue();
        }
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(
            new UuidRule('valid-uuidv4', $options)
        );

        return $rules;
    }
}
