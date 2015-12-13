<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;

/**
 * Sanitized the input value to be a UUID v4.
 */
class UuidRule extends Rule
{
    const OPTION_TRIM = 'trim';

    protected function execute($value, EntityInterface $entity = null)
    {
        if (!is_string($value)) {
            $this->throwError('invalid_type', [ 'value' => $value ]);
            return false;
        }

        $trim = $this->getOption(self::OPTION_TRIM, false);
        if ($trim) {
            $value = trim($value);
        }

        $match_count = preg_match(
            '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i',
            $value
        );

        if ($match_count !== 1) {
            $this->throwError('invalid_uuidv4', [ 'value' => $value ]);
            return false;
        }

        $this->setSanitizedValue($value);

        return true;
    }
}
