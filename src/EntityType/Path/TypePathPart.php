<?php

namespace Trellis\EntityType\Path;

final class TypePathPart
{
    /**
     * @var string $attribute_name
     */
    private $attribute_name;

    /**
     * @var string $type
     */
    private $type;

    /**
     * @param string $attribute_name
     * @param string $type
     */
    public function __construct(string $attribute_name, string $type = '')
    {
        $this->attribute_name = $attribute_name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->attribute_name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function hasType(): bool
    {
        return !empty($this->type);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->hasType()
            ? $this->getAttributeName().'.'.$this->getType()
            : $this->getAttributeName();
    }
}
