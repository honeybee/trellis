<?php

namespace Trellis\Value;

use Trellis\Collection\TypedMap;
use Trellis\Entity\EntityInterface;

class ValueMap extends TypedMap
{
    protected $parent;

    public function __construct(EntityInterface $parent, array $data = [])
    {
        $this->parent = $parent;

        $values = [];
        foreach ($parent->type()->getAttributes() as $name => $attribute) {
            $values[$name] = $attribute->createValue(
                $this->parent,
                isset($data[$name]) ? $data[$name] : null
            );
        }

        parent::__construct(ValueInterface::CLASS, $values);
    }
}
