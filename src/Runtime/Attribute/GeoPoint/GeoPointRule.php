<?php

namespace Trellis\Runtime\Attribute\GeoPoint;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;

class GeoPointRule extends Rule
{
    protected function execute($value, EntityInterface $entity = null)
    {
        try {
            if (is_array($value)) {
                if (!empty($value) && !$this->isAssoc($value)) {
                    $this->throwError('non_assoc_array', [ 'value' => $value ], IncidentInterface::CRITICAL);
                    return false;
                }
                $geopoint = GeoPoint::createFromArray($value);
            } elseif ($value instanceof GeoPoint) {
                $geopoint = GeoPoint::createFromArray($value->toNative());
            } else {
                $this->throwError(
                    'invalid_type',
                    [ 'value' => $value, 'type' => gettype($value) ],
                    IncidentInterface::CRITICAL
                );
                return false;
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
