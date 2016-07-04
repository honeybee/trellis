<?php

namespace Trellis\EntityType\Path;

use Assert\Assertion;

class TypePathPart
{
    protected $attribute_name;

    protected $type;

    public function __construct($attribute_name, $type = null)
    {
        Assertion::string($attribute_name);
        Assertion::nullOrString($type);

        $this->attribute_name = $attribute_name;
        $this->type = $type;
    }

    public function getAttributeName()
    {
        return $this->attribute_name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function hasType()
    {
        return !is_null($this->type);
    }

    public function __toString()
    {
        $path = $this->getAttributeName();
        if ($this->hasType()) {
            $path .= '.'.$this->getType();
        }
        return $path;
    }
}
