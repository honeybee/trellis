<?php

namespace Trellis\Runtime\Attribute\GeoPoint;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Result\IncidentMap;
use Trellis\Runtime\Validator\Rule\Rule;

class GeoPointRule extends Rule
{
    const OPTION_NULL_ISLAND_AS_NULL = 'null_island_as_null';

    /**
     * @overridden
     */
    public function apply($value, EntityInterface $entity = null)
    {
        $this->incidents = new IncidentMap();
        $this->sanitized_value = null;

        // $this->setSanitizedValue() must be called in execute() to accept/set valid values
        // this allows us to set NULL as a valid value
        return $this->execute($value, $entity);
    }

    protected function execute($value, EntityInterface $entity = null)
    {
        $null_value = $this->getOption(AttributeInterface::OPTION_NULL_VALUE, null);
        if ($value === $null_value || $value === '') {
            // accept NULL or empty string as valid NULL value when NULL value is NULL :D
            $this->setSanitizedValue($null_value);
            return true;
        }

        try {
            if (is_array($value)) {
                /**
                 * @var GeoPoint
                 */
                $geopoint = GeoPoint::createFromArray($value);
            } elseif ($value instanceof GeoPoint) {
                /**
                 * @var GeoPoint
                 */
                $geopoint = GeoPoint::createFromArray($value->toNative());
            } else {
                $this->throwError(
                    'invalid_type',
                    [ 'value' => $value, 'type' => gettype($value) ],
                    IncidentInterface::CRITICAL
                );
                return false;
            }

            $treat_null_island_as_null = $this->getOption(self::OPTION_NULL_ISLAND_AS_NULL, true);
            if ($treat_null_island_as_null === true && $geopoint->isNullIsland()) {
                // use (0,0) as NULL as that's a geocoding failure or not wanted or wanted as NULL trigger
                $this->setSanitizedValue($null_value);
                return true;
            }

            // set the sanitized new geopoint data
            $this->setSanitizedValue($geopoint);
        } catch (BadValueException $e) {
            $this->throwError(
                'invalid_data',
                [
                    'error' => $e->getMessage()
                ],
                IncidentInterface::CRITICAL
            );
            return false;
        }

        return true;
    }
}
