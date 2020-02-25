<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Entity\EntityList;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;

/**
 * ReferenceRule validates that a given value consistently translates to a collection of entities.
 *
 * Supported options: reference_types
 */
class ReferenceRule extends Rule
{
    /**
     * Option that holds a list of allowed types to validate against.
     */
    const OPTION_REFERENCE_MODULES = 'reference_types';

    /**
     * Valdiates and sanitizes a given value respective to the reference-valueholder's expectations.
     *
     * @param mixed $value The types 'array' and 'EntityList' are accepted.
     *
     * @return boolean
     */
    protected function execute($value, EntityInterface $entity = null)
    {
        $success = true;
        $collection = null;

        if ($value instanceof EntityList) {
            $collection = $value;
        } elseif (null === $value) {
            $collection = new EntityList();
        } elseif (is_array($value)) {
            $collection = $this->createEntityList($value);
        } else {
            $this->throwError('invalid_type');
            $success = false;
        }

        if ($success) {
            $this->setSanitizedValue($collection);
        }

        return $success;
    }

    /**
     * Create a EntityList from a given array of entity data.
     *
     * @param array $entities_data
     *
     * @return EntityList
     */
    protected function createEntityList(array $entities_data)
    {
        $type_map = [];
        foreach ($this->getOption(self::OPTION_REFERENCE_MODULES, []) as $type) {
            $type_map[$type->getEntityType()] = $type;
        }

        $collection = new EntityList();
        ksort($entities_data);
        foreach ($entities_data as $entity_data) {
            if (!isset($entity_data[self::OBJECT_TYPE])) {
                $this->throwError('missing_doc_type', [], IncidentInterface::CRITICAL);
                continue;
            }

            $reference_type = $entity_data[self::OBJECT_TYPE];
            unset($entity_data['@type']);

            if ($reference_type[0] !== '\\') {
                $reference_type = '\\' . $reference_type;
            }
            if (!isset($type_map[$reference_type])) {
                $this->throwError(
                    'invalid_doc_type',
                    array('type' => @$entity_data[self::OBJECT_TYPE]),
                    IncidentInterface::NOTICE
                );
                continue;
            }

            $reference_type = $type_map[$reference_type];
            $collection->push($reference_type->createEntity($entity_data));
        }

        return $collection;
    }
}
