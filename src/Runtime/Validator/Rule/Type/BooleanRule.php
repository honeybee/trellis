<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;

/**
 * Sanitized the input value to be boolean.
 *
 * Treats:
 *
 * - "1", "true", "on" and "yes" as TRUE
 * - "0", "false", "off", "no" and empty string as FALSE
 * - everything else (e.g. string 'null') as NULL and thus throws an validation error
 */
class BooleanRule extends Rule
{
    protected function execute($value, EntityInterface $entity = null)
    {
        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $bool || is_object($value) || $value === null) {
            // FILTER_VALIDATE_BOOLEAN treats objects, NULL as boolean FALSE…
            $this->throwError('invalid_boolean', [ 'value' => $value ]);
            return false;
        }

        $this->setSanitizedValue($bool);

        return true;
    }
}
