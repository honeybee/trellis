<?php

namespace Trellis\Value;

use Equip\Structure\Dictionary;
use Trellis\Entity\EntityTypeInterface;

class ValueMap extends Dictionary implements ValueMapInterface
{
    public function __construct(EntityTypeInterface $type, array $data = [])
    {
        $values = [];
        foreach ($type->getAttributes() as $name => $attribute) {
            if (array_key_exists($name, $data)) {
                $values[$name] = $attribute->createValue($data[$name]);
            }
        }

        parent::__construct($values);
    }
}
