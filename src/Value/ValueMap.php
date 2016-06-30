<?php

namespace Trellis\Value;

use Trellis\Collection\TypedMap;
use Trellis\Entity\EntityTypeInterface;

class ValueMap extends TypedMap implements ValueMapInterface
{
    protected $type;

    public function __construct(EntityTypeInterface $type, array $data = [])
    {
        $this->type = $type;

        $values = [];
        foreach ($type->getAttributes() as $name => $attribute) {
            if (array_key_exists($name, $data)) {
                $values[$name] = $attribute->createValue($data[$name]);
            }
        }

        parent::__construct(ValueInterface::CLASS, $values);
    }
}
