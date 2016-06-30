<?php

namespace Trellis\Path;

use Assert\Assertion;

class ValuePathPart implements PathPartInterface
{
    protected $attribute_name;

    protected $position;

    public function __construct($attribute_name, $position = null)
    {
        Assertion::string($attribute_name);
        Assertion::nullOrInteger($position);

        $this->attribute_name = $attribute_name;
        $this->position = $position;
    }

    public function getAttributeName()
    {
        return $this->attribute_name;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function hasPosition()
    {
        return !is_null($this->position);
    }

    public function __toString()
    {
        $path = $this->getAttributeName();
        if ($this->hasPosition()) {
            $path .= '.'.$this->getPosition();
        }
        return $path;
    }
}
