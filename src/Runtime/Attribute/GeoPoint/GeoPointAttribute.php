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
    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new GeoPointRule('valid-geopoint', $options));

        return $rules;
    }
}
