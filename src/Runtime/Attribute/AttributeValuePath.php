<?php

namespace Trellis\Runtime\Attribute;

use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Trellis\Common\Error\RuntimeException;
use Trellis\Runtime\Entity\EntityInterface;

class AttributeValuePath
{
    const PATH_DELIMITER = '.';

    public static function getAttributeValueByPath(EntityInterface $entity, $value_path)
    {
        if (!mb_strpos($value_path, self::PATH_DELIMITER)) {
            return $entity->getValue($value_path);
        }

        // prepare path tuples
        $split_path = self::splitPath($value_path);
        $path_tuples = $split_path['path_tuples'];
        $target_attribute = $split_path['target_attribute'];
        $current_type = $entity->getType();
        $current_entity = $entity;
        // loop into path
        foreach ($path_tuples as $path_tuple) {
            $offset_spec = self::parseOffsetExpression($path_tuple[1]);
            $current_attribute = $current_type->getAttribute($path_tuple[0]);
            $entity_collection = $current_entity->getValue($current_attribute->getName());
            // try to find the next entity that matches the current offset_spec
            $type_offsets = array('_all' => 0);
            $current_entity = null;
            foreach ($entity_collection as $next_entity) {
                $type_prefix = $next_entity->getType()->getPrefix();
                if (!isset($type_offsets[$type_prefix])) {
                    $type_offsets[$type_prefix] = 0;
                }
                if (self::entityMatchesOffsetSpec($next_entity, $offset_spec, $type_offsets)) {
                    $current_entity = $next_entity;
                    break;
                }
                $type_offsets['_all']++;
                $type_offsets[$type_prefix]++;
            }
            // the value_path/offset_spec is valid, but doesn't match any entities in question
            if (!$current_entity) {
                return null;
            }
            // prepare for next iteration by switching the current_type to the next level
            if ($current_attribute instanceof EmbeddedEntityListAttribute) {
                $current_type = $current_attribute->getEmbeddedTypeByPrefix($offset_spec['entity_type']);
            } else {
                throw new RuntimeException(
                    'Invalid attribute-type given within attribute-value-path.' .
                    'Only Reference- and EmbeddedEntityListAttributes are supported.'
                );
            }
        }

        return $target_attribute ? $current_entity->getValue($target_attribute) : $current_entity;
    }

    protected static function splitPath($value_path)
    {
        $path_tuples = [];
        $path_parts = explode(self::PATH_DELIMITER, $value_path);
        $next_tuple = [];
        $parts_count = count($path_parts);
        $target_attribute = null;
        $points_to_attribute = ($parts_count % 2) !== 0;
        $iteration_limit = $points_to_attribute ? $parts_count - 1 : $parts_count;
        for ($i = 0; $i < $iteration_limit; $i++) {
            $next_tuple[] = $path_parts[$i];
            if (($i + 1) % 2 === 0) {
                $path_tuples[] = $next_tuple;
                $next_tuple = [];
            }
            $is_last = ($i + 1) === $iteration_limit;
            if ($is_last && $points_to_attribute) {
                $target_attribute = $path_parts[$i + 1];
            }
        }

        return array(
            'path_tuples' => $path_tuples,
            'target_attribute' => $target_attribute
        );
    }

    protected static function parseOffsetExpression($offset_expression)
    {
        $compare_attribute = null;
        $compare_value = null;

        if (preg_match('~(\w+|\*)(?:\[([\w="\-]+)\])~is', $offset_expression, $matches)) {
            $entity_type = $matches[1];
            $collection_offset = $matches[2];
        } elseif (preg_match('~(\w+|\*)~is', $offset_expression, $matches)) {
            $entity_type = $matches[1];
            $collection_offset = '*';
        } else {
            throw new RuntimeException(
                "Missing or invalid offset specification within attribute-value-path: " . $offset_expression
            );
        }

        if (preg_match('~(\w+)="([\w\-_]+)"~is', $collection_offset, $matches) && count($matches) === 3) {
            $compare_attribute = $matches[1];
            $compare_value = $matches[2];
        } else {
            $collection_offset = (int)$collection_offset;
        }

        if ($compare_attribute) {
            return array(
                'type' => 'attribute',
                'entity_type' => $entity_type,
                'attribute_name' => $compare_attribute,
                'attribute_value' => $compare_value
            );
        } else {
            return array(
                'type' => 'index',
                'entity_type' => $entity_type,
                'position' => $collection_offset
            );
        }
    }

    protected static function entityMatchesOffsetSpec($entity, $offset_spec, array $type_offsets)
    {
        $type_prefix = $entity->getType()->getPrefix();
        $offset = $type_offsets['_all'];

        if ($offset_spec['entity_type'] !== $type_prefix && $offset_spec['entity_type'] !== '*') {
            return false;
        } elseif ($offset_spec['entity_type'] === $type_prefix) {
            $offset = $type_offsets[$type_prefix];
        }

        if ($offset_spec['type'] === 'index') {
            return $offset === $offset_spec['position'] || $offset_spec['position'] === '*';
        } else {
            return $entity->getValue($offset_spec['attribute_name']) === $offset_spec['attribute_value'];
        }
    }
}
