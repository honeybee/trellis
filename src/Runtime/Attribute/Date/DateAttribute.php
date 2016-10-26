<?php

namespace Trellis\Runtime\Attribute\Date;

use Trellis\Runtime\Attribute\Timestamp\TimestampAttribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\DateRule;

// preferred exchange format is FORMAT_ISO8601 ('Y-m-d\TH:i:s.uP')
class DateAttribute extends TimestampAttribute
{
    const FORMAT_NATIVE = TimestampAttribute::FORMAT_ISO8601_SIMPLE;

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $valid_date_rule = new DateRule('valid-date', $options);

        $rules->push($valid_date_rule);

        return $rules;
    }
}
