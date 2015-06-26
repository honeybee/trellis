<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Attribute\AttributeInterface;

class IntegerRule extends Rule
{
    const OPTION_ALLOW_HEX = 'allow_hex';
    const OPTION_ALLOW_OCTAL = 'allow_octal';
    const OPTION_MIN_VALUE = 'min_value';
    const OPTION_MAX_VALUE = 'max_value';

    protected function execute($value, EntityInterface $entity = null)
    {
        if ($value === '') {
            $value = $this->getOption(
                AttributeInterface::OPTION_NULL_VALUE,
                $this->getOption(
                    self::OPTION_MIN_VALUE,
                    $this->getOption(self::OPTION_MAX_VALUE, 0)
                )
            );

            $this->setSanitizedValue($value);

            return true;
        }

        $allow_hex = $this->toBoolean($this->getOption(self::OPTION_ALLOW_HEX, false));
        $allow_octal = $this->toBoolean($this->getOption(self::OPTION_ALLOW_OCTAL, false));

        $filter_flags = 0;
        if ($allow_hex) {
            $filter_flags |= FILTER_FLAG_ALLOW_HEX;
        }
        if ($allow_octal) {
            $filter_flags |= FILTER_FLAG_ALLOW_OCTAL;
        }

        $sanitized = [];

        $int = filter_var($value, FILTER_VALIDATE_INT, $filter_flags);

        if ($int === false || $value === true) {
            // filter_var validates bool TRUE to 1 (while bool FALSE is invalid) -.-
            $this->throwError('non_integer_value', [ 'value' => $value ]);
            return false;
        }

        // check minimum value
        if ($this->hasOption(self::OPTION_MIN_VALUE)) {
            $min = filter_var(
                $this->getOption(self::OPTION_MIN_VALUE, -PHP_INT_MAX-1),
                FILTER_VALIDATE_INT,
                $filter_flags
            );
            if ($min === false) {
                throw new InvalidConfigException('Minimum value specified is not interpretable as integer.');
            }

            if ($int < $min) {
                $this->throwError(self::OPTION_MIN_VALUE, [
                    self::OPTION_MIN_VALUE => $min,
                    'value' => $int
                ]);
                return false;
            }
        }

        // check maximum value
        if ($this->hasOption(self::OPTION_MAX_VALUE)) {
            $max = filter_var(
                $this->getOption(self::OPTION_MAX_VALUE, PHP_INT_MAX),
                FILTER_VALIDATE_INT,
                $filter_flags
            );
            if ($max === false) {
                throw new InvalidConfigException('Maximum value specified is not interpretable as integer.');
            }

            if ($int > $max) {
                $this->throwError(self::OPTION_MAX_VALUE, [
                    self::OPTION_MAX_VALUE => $max,
                    'value' => $int
                ]);
                return false;
            }
        }

        $this->setSanitizedValue($int);

        return true;
    }
}
