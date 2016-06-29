<?php

namespace Trellis\Path;

class ValuePathPart implements TrellisPathPartInterface
{
    protected $attribute_name;

    protected $position;

    public function __construct($attribute_name, $position)
    {
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

    public function __toString()
    {
        $path = $this->getAttributeName();
        if ($this->hasPosition()) {
            $path .= '.'.$this->getPosition();
        }
        return $path;
    }
}
