<?php

namespace Trellis\Entity\Path;

final class ValuePathPart
{
    /**
     * @var string $attribute_name
     */
    private $attribute_name;

    /**
     * @var int $position
     */
    private $position;

    /**
     * @param string $attribute_name
     *
     * @param int $position
     */
    public function __construct(string $attribute_name, int $position = -1)
    {
        $this->attribute_name = $attribute_name;
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->attribute_name;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return boolean
     */
    public function hasPosition(): bool
    {
        return $this->position >= 0;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->hasPosition()
            ? $this->getAttributeName().".".$this->getPosition()
            : $this->getAttributeName();
    }
}
