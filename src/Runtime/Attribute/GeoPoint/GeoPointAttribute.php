<?php

namespace Trellis\Runtime\Attribute\GeoPoint;

use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Attribute\HasComplexValueInterface;
use Trellis\Runtime\Validator\Rule\RuleList;

/**
 * A lon/lat GeoPoint.
 */
class GeoPointAttribute extends Attribute implements HasComplexValueInterface
{
    const OPTION_NULL_ISLAND_AS_NULL = GeoPointRule::OPTION_NULL_ISLAND_AS_NULL;

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new GeoPointRule('valid-geopoint', $options));

        return $rules;
    }
}
