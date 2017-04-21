<?php

namespace Trellis\EntityType\Path;

final class TypePathPart
{
    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $attributeName
     * @param string $type
     */
    public function __construct(string $attributeName, string $type = "")
    {
        $this->attributeName = $attributeName;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->attributeName;
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
            ? $this->getAttributeName().".".$this->getType()
            : $this->getAttributeName();
    }
}
