<?php

namespace Trellis\Runtime\Attribute\BooleanList;

use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;

class BooleanListRule extends Rule
{
    protected function execute($values, EntityInterface $entity = null)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $sanitized = [];

        foreach ($values as $value) {
            $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if (null === $bool || is_object($value) || $value === "" || $value === null) {
                // FILTER_VALIDATE_BOOLEAN treats objects, NULL and empty strings as boolean FALSE… -.-
                $this->throwError('invalid_type', [ 'value' => $value ]);
                return false;
            }

            $sanitized[] = $bool;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }
}
