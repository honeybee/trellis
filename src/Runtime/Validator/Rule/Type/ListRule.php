<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Traversable;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Rule\Rule;

class ListRule extends Rule
{
    const OPTION_CAST_TO_ARRAY = 'cast_to_array';
    const OPTION_MAX_COUNT = 'max_count';
    const OPTION_MIN_COUNT = 'min_count';
    const OPTION_REINDEX_LIST = 'reindex_list';

    protected function execute($value, EntityInterface $entity = null)
    {
        $cast_to_array = $this->getOption(self::OPTION_CAST_TO_ARRAY, true);
        if ((!$cast_to_array && !is_array($value)) || (!$cast_to_array && !$value instanceof Traversable)) {
            $this->throwError('not_an_array');
            return false;
        }

        $success = true;

        $casted = [];
        if (is_array($value)) {
            $casted = $value;
        } elseif ($value instanceof Traversable) {
            foreach ($value as $key => $item) {
                $casted[$key] = $item;
            }
        } else {
            $casted = [ $value ];
        }

        $count = count($casted);

        if ($this->hasOption(self::OPTION_MIN_COUNT)) {
            $min_count = $this->getOption(self::OPTION_MIN_COUNT, 0);
            if ($count < (int)$min_count) {
                $this->throwError(
                    self::OPTION_MIN_COUNT,
                    [
                        'count' => $count,
                        self::OPTION_MIN_COUNT => $min_count
                    ]
                );
                $success = false;
            }
        }

        if ($this->hasOption(self::OPTION_MAX_COUNT)) {
            $max_count = $this->getOption(self::OPTION_MAX_COUNT, 0);
            if ($count > (int)$max_count) {
                $this->throwError(
                    self::OPTION_MAX_COUNT,
                    [
                        'count' => $count,
                        self::OPTION_MAX_COUNT => $max_count
                    ]
                );
                $success = false;
            }
        }

        if ($this->getOption(self::OPTION_REINDEX_LIST, false)) {
            // e.g. useful to reorder nested POST data when moving of input fields happened w/o input name changing
            // example: foo[1][bar], foo[0][bar], foo[3][bar] => foo[0][bar], foo[1][bar], foo[3][bar]
            $casted = array_values($casted);
        }

        // export valid values
        if ($success) {
            $this->setSanitizedValue($casted);
        }

        return $success;
    }
}
