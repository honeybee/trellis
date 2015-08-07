<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Common\Error\InvalidConfigException;

class TokenRule extends Rule
{
    const OPTION_MANDATORY    = 'mandatory';
    const OPTION_MIN_LENGTH   = 'min_length';
    const OPTION_MAX_LENGTH   = 'max_length';

    protected function execute($value, EntityInterface $entity = null)
    {
        if (!is_scalar($value) || !is_string($value)) {
            $this->throwError('invalid_type', [], IncidentInterface::CRITICAL);
            return false;
        }

        $null_value = $this->getOption(AttributeInterface::OPTION_NULL_VALUE, '');
        if ($value === $null_value) {
            $this->setSanitizedValue($null_value);
            return true;
        }

        $sanitized_value = $value;

        // check minimum string length
        if ($this->hasOption(self::OPTION_MIN_LENGTH)) {
            $min = filter_var($this->getOption(self::OPTION_MIN_LENGTH, -PHP_INT_MAX-1), FILTER_VALIDATE_INT);
            if ($min === false) {
                throw new InvalidConfigException('Minimum token length specified is not interpretable as integer.');
            }
            if (mb_strlen($sanitized_value) < $min) {
                $this->throwError(
                    self::OPTION_MIN_LENGTH,
                    [ self::OPTION_MIN_LENGTH => $min, 'value' => $sanitized_value ]
                );
                return false;
            }
        }

        // check maximum string length
        if ($this->hasOption(self::OPTION_MAX_LENGTH)) {
            $max = filter_var($this->getOption(self::OPTION_MAX_LENGTH, PHP_INT_MAX), FILTER_VALIDATE_INT);
            if ($max === false) {
                throw new InvalidConfigException('Maximum token length specified is not interpretable as integer.');
            }
            if (mb_strlen($sanitized_value) > $max) {
                $this->throwError(
                    self::OPTION_MAX_LENGTH,
                    [ self::OPTION_MAX_LENGTH => $max, 'value' => $sanitized_value ]
                );
                return false;
            }
        }

        // check is acceptable token
        if (preg_match('/[a-f0-9]+/i', $sanitized_value) === false) {
            $this->throwError('invalid_characters', [ 'value' => $sanitized_value ], IncidentInterface::CRITICAL);
            return false;
        }

        $this->setSanitizedValue($sanitized_value);

        return true;
    }
}
