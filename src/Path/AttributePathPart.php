<?php

namespace Trellis\Path;

class AttributePathPart implements TrellisPathPartInterface
{
    protected $attribute_name;

    protected $type;

    public function __construct($attribute_name, $type = null)
    {
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
        return $this->type !== null;
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
