<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Common\Error\InvalidConfigException;

class TokenRule extends Rule
{
    const OPTION_MIN_LENGTH                   = 'min_length';
    const OPTION_MAX_LENGTH                   = 'max_length';

    protected function execute($value, EntityInterface $entity = null)
    {
        if (!is_string($value)) {
            $this->throwError('invalid_type', []);
            return false;
        }

        // check minimum string length
        if ($this->hasOption(self::OPTION_MIN_LENGTH)) {
            $min = filter_var($this->getOption(self::OPTION_MIN_LENGTH, -PHP_INT_MAX-1), FILTER_VALIDATE_INT);
            if ($min === false) {
                throw new InvalidConfigException('Minimum token length specified is not interpretable as integer.');
            }
            if (mb_strlen($value) < $min) {
                $this->throwError(
                    self::OPTION_MIN_LENGTH,
                    [ self::OPTION_MIN_LENGTH => $min, 'value' => $value ]
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
            if (mb_strlen($value) > $max) {
                $this->throwError(
                    self::OPTION_MAX_LENGTH,
                    [ self::OPTION_MAX_LENGTH => $max, 'value' => $value ]
                );
                return false;
            }
        }

        // check is acceptable token
        if (preg_match('/[a-f0-9]+/i', $value) === false) {
            $this->throwError('invalid_token', [ 'value' => $value ]);
            return false;
        }

        $this->setSanitizedValue($value);

        return true;
    }
}
