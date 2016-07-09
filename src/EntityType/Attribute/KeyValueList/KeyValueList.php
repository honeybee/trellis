<?php

namespace Trellis\EntityType\Attribute\KeyValueList;

use Assert\Assertion;
use Trellis\Collection\Map;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class KeyValueList implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var Map $map
     */
    private $map;

    /**
     * @param string $key_value_list
     */
    public function __construct(array $key_value_list = [])
    {
        Assertion::isArray($key_value_list, 'KeyValueList may only be constructed from an array.');

        $this->map = new Map($key_value_list);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->map->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return iterator_to_array($this->map);
    }
}
