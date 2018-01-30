<?php

namespace Trellis\CodeGen\Schema;

use Trellis\Common\BaseObject;

class OptionDefinition extends BaseObject
{
    protected $name;

    protected $value;

    protected $default;

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getDefault()
    {
        return $this->default;
    }
}
