<?php

namespace Trellis\Runtime\Attribute\EmbeddedEntityList;

use Trellis\Runtime\EntityTypeMap;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Entity\EntityList;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;

/**
 * Validates that a given (array) value consistently translates to a list of entities.
 *
 * Supported options: entity_types
 */
class EmbeddedEntityListRule extends Rule
{
    /**
     * Option that holds a list of allowed types to validate against.
     */
    const OPTION_ENTITY_TYPES = 'entity_types';
    const OPTION_MAX_COUNT = 'max_count';
    const OPTION_MIN_COUNT = 'min_count';

    /**
     * Validates and sanitizes a given value respective to the valueholder's expectations.
     *
     * @param mixed $value The types 'array' and 'EntityList' are accepted.
     *
     * @return boolean
     */
    protected function execute($value, EntityInterface $parent_entity = null)
    {
        $success = true;
        $list = null;

        if ($value instanceof EntityList) {
            $list = $value;
        } elseif (null === $value) {
            $list = new EntityList();
        } elseif ($value instanceof EntityInterface) {
            $list = new EntityList();
            $list->push($value);
        } elseif (is_array($value)) {
            $list = new EntityList();
            $success = $this->createEntityList($value, $list, $parent_entity);
        } else {
            $this->throwError('invalid_value_type');
            return false;
        }

        $count = count($list);
        if ($this->hasOption(self::OPTION_MIN_COUNT)) {
            $min_count = $this->getOption(self::OPTION_MIN_COUNT, 0);
            if ($count < (int)$min_count) {
                $this->throwError('min_count', [ 'count' => $count, 'min_count' => $min_count ]);
                $success = false;
            }
        }

        if ($this->hasOption(self::OPTION_MAX_COUNT)) {
            $max_count = $this->getOption(self::OPTION_MAX_COUNT, 0);
            if ($count > (int)$max_count) {
                $this->throwError('max_count', [ 'count' => $count, 'max_count' => $max_count ]);
                $success = false;
            }
        }

// foreach ($this->getIncidents() as $incident) {
//     echo PHP_EOL.'incident: '. $incident->getName();
// }

        if ($success) {
            $this->setSanitizedValue($list);
            return true;
        }

        return false;
    }

    /**
     * Create a EntityList from a given array of entity data.
     *
     * @param array $entities_data
     *
     * @return EntityList
     */
    protected function createEntityList(array $entities_data, EntityList $list, EntityInterface $parent_entity = null)
    {
        $success = true;

        $embedded_entity_type_map = $this->getOption(self::OPTION_ENTITY_TYPES, new EntityTypeMap);

        foreach ($entities_data as $embedded_entity_data) {
            if (!isset($embedded_entity_data[self::OBJECT_TYPE])) {
                $success = false;
                $this->throwError('missing_embed_prefix', [], IncidentInterface::CRITICAL);
                continue;
            }

            $embed_prefix = $embedded_entity_data[self::OBJECT_TYPE];
            if (!$embedded_entity_type_map->hasKey($embed_prefix)) {
                //var_dump(array_keys($type_map), $trimmed_embed_type);exit;
                $success = false;
                $this->throwError(
                    'invalid_embed_prefix',
                    [ 'type' => var_export($embedded_entity_data[self::OBJECT_TYPE], true) ],
                    IncidentInterface::CRITICAL
                );
                continue;
            }
            unset($embedded_entity_data['@type']);

            $embedded_type = $embedded_entity_type_map->getItem($embed_prefix);
            $list->push($embedded_type->createEntity($embedded_entity_data, $parent_entity));
        }

        return $success;
    }
}
