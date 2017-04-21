<?php

namespace Trellis\Entity\Path;

final class ValuePathPart
{
    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var int
     */
    private $position;

    /**
     * @param string $attributeName
     * @param int $position
     */
    public function __construct(string $attributeName, int $position = -1)
    {
        $this->attributeName = $attributeName;
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getAttributeName(): string
    {
        return $this->attributeName;
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
