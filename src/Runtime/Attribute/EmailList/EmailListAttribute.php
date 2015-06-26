<?php

namespace Trellis\Runtime\Attribute\EmailList;

use Trellis\Runtime\Attribute\ListAttribute;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\ListRule;

/**
 * A list of key => value pairs where:
 *
 * - key is an email string
 * - value is a string (label/name)
 *
 */
class EmailListAttribute extends ListAttribute
{
    const OPTION_ALLOWED_EMAILS         = EmailListRule::OPTION_ALLOWED_EMAILS;
    const OPTION_ALLOWED_EMAIL_LABELS   = EmailListRule::OPTION_ALLOWED_EMAIL_LABELS;
    const OPTION_ALLOWED_EMAIL_PAIRS    = EmailListRule::OPTION_ALLOWED_EMAIL_PAIRS;
    const OPTION_MAX_EMAIL_LABEL_LENGTH = EmailListRule::OPTION_MAX_EMAIL_LABEL_LENGTH;
    const OPTION_MIN_EMAIL_LABEL_LENGTH = EmailListRule::OPTION_MIN_EMAIL_LABEL_LENGTH;

    const OPTION_MAX_COUNT              = EmailListRule::OPTION_MAX_COUNT;
    const OPTION_MIN_COUNT              = EmailListRule::OPTION_MIN_COUNT;
    const OPTION_CAST_TO_ARRAY          = EmailListRule::OPTION_CAST_TO_ARRAY;

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new EmailListRule('valid-email-list', $options));

        return $rules;
    }
}
