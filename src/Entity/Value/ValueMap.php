<?php

namespace Trellis\Entity\Value;

use Trellis\Collection\TypedMap;
use Trellis\Entity\EntityInterface;

class ValueMap extends TypedMap
{
    /**
     * @var EntityInterface $parent
     */
    protected $parent;

    /**
     * @param EntityInterface $parent
     * @param mixed[] $data
     */
    public function __construct(EntityInterface $parent, array $data = [])
    {
        $this->parent = $parent;

        $values = [];
        foreach ($parent->type()->getAttributes() as $name => $attribute) {
            $values[$name] = $attribute->createValue(
                $this->parent,
                array_key_exists($name, $data) ? $data[$name] : null
            );
        }

        parent::__construct(ValueInterface::CLASS, $values);
    }
}
