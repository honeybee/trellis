<?php

namespace Trellis\Runtime\Attribute\IntegerList;

use Trellis\Common\Error\InvalidConfigException;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;

class IntegerListRule extends Rule
{
    const OPTION_ALLOW_HEX = 'allow_hex';
    const OPTION_ALLOW_OCTAL = 'allow_octal';
    const OPTION_MIN_VALUE = 'min_value';
    const OPTION_MAX_VALUE = 'max_value';

    protected function execute($values, EntityInterface $entity = null)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $allow_hex = $this->getOption(self::OPTION_ALLOW_HEX, false);
        $allow_octal = $this->getOption(self::OPTION_ALLOW_OCTAL, false);

        $filter_flags = 0;
        if ($allow_hex) {
            $filter_flags |= FILTER_FLAG_ALLOW_HEX;
        }
        if ($allow_octal) {
            $filter_flags |= FILTER_FLAG_ALLOW_OCTAL;
        }

        $sanitized = [];

        // validate that each value of the array is a valid integer
        foreach ($values as $int) {
            $value = filter_var($int, FILTER_VALIDATE_INT, $filter_flags);

            if ($value === false || $int === true) {
                // filter_var validates bool TRUE to 1 (while bool FALSE is invalid) -.-
                $this->throwError('non_integer_value', [ 'value' => $int ]);
                return false;
            }

            // check minimum value
            if ($this->hasOption(self::OPTION_MIN_VALUE)) {
                $min = filter_var(
                    $this->getOption(self::OPTION_MIN_VALUE),
                    FILTER_VALIDATE_INT,
                    $filter_flags
                );

                if ($min === false) {
                    throw new InvalidConfigException('Minimum value specified is not interpretable as integer.');
                }

                if ($value < $min) {
                    $this->throwError(self::OPTION_MIN_VALUE, [
                        self::OPTION_MIN_VALUE => $min,
                        'value' => $value
                    ]);
                    return false;
                }
            }

            // check maximum value
            if ($this->hasOption(self::OPTION_MAX_VALUE)) {
                $max = filter_var(
                    $this->getOption(self::OPTION_MAX_VALUE),
                    FILTER_VALIDATE_INT,
                    $filter_flags
                );

                if ($max === false) {
                    throw new InvalidConfigException('Maximum value specified is not interpretable as integer.');
                }

                if ($value > $max) {
                    $this->throwError(self::OPTION_MAX_VALUE, [
                        self::OPTION_MAX_VALUE => $max,
                        'value' => $value
                    ]);
                    return false;
                }
            }

            $sanitized[] = $value;
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }
}
