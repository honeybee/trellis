<?php

namespace Trellis\Runtime;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueKeyInterface;
use Trellis\Common\Collection\UniqueValueInterface;
use Trellis\Common\Error\RuntimeException;

class EntityTypeMap extends TypedMap implements UniqueKeyInterface, UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return EntityTypeInterface::CLASS;
    }

    public function getByClassName($fqcn)
    {
        $fqcn = trim($fqcn, "\\");
        $matched_types = $this->filter(
            function ($entity_type) use ($fqcn) {
                return get_class($entity_type) === $fqcn;
            }
        );

        if ($matched_types->getSize() !== 1) {
            throw new RuntimeException(
                sprintf(
                    'Unexpected number of %d matching types for %s call and given class name: %s',
                    $matched_types->getSize(),
                    __METHOD__,
                    $fqcn
                )
            );
        }

        $types_arr = $matched_types->getValues();

        return $types_arr[0];
    }

    public function getByEntityImplementor($fqcn)
    {
        $fqcn = trim($fqcn, "\\");
        $matched_types = $this->filter(
            function ($entity_type) use ($fqcn) {
                $impl = $entity_type::getEntityImplementor();
                $impl = ltrim($impl, "\\");
                return $impl === $fqcn;
            }
        );

        if ($matched_types->getSize() !== 1) {
            throw new RuntimeException(
                sprintf(
                    'Unexpected number of %d matching types for %s call and given class name: %s',
                    $matched_types->getSize(),
                    __METHOD__,
                    $fqcn
                )
            );
        }

        $types_arr = $matched_types->getValues();

        return $types_arr[0];
    }
}
