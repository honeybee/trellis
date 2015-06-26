<?php

namespace Trellis\Runtime\Attribute;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Type\ListRule;

/**
 * Attribute that has a ListValueHolder (usually an array internally).
 */
abstract class ListAttribute extends Attribute
{
    const OPTION_CAST_TO_ARRAY  = ListRule::OPTION_CAST_TO_ARRAY;
    const OPTION_MAX_COUNT      = ListRule::OPTION_MAX_COUNT;
    const OPTION_MIN_COUNT      = ListRule::OPTION_MIN_COUNT;
    const OPTION_REINDEX_LIST   = ListRule::OPTION_REINDEX_LIST;

    /**
     * Returns an attribute's null value.
     *
     * @return mixed value to be used/interpreted as null (not set)
     */
    public function getNullValue()
    {
        return [];
    }

    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new ListRule('valid-list', $options);

        $rules->push($rule);

        return $rules;
    }
}
