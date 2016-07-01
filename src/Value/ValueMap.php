<?php

namespace Trellis\Value;

use Trellis\Collection\TypedMap;
use Trellis\Entity\EntityTypeInterface;

class ValueMap extends TypedMap
{
    protected $type;

    public function __construct(EntityTypeInterface $type, array $data = [])
    {
        $this->type = $type;

        $values = [];
        foreach ($type->getAttributes() as $name => $attribute) {
            $values[$name] = $attribute->createValue(
                isset($data[$name]) ? $data[$name] : null
            );
        }

        parent::__construct(ValueInterface::CLASS, $values);
    }
}
