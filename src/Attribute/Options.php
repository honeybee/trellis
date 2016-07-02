<?php

namespace Trellis\Attribute;

use Trellis\Collection\Map;

class Options extends Map
{
    /**
     * @param string $key
     * @param mixed $default
     * @param boolean $fluent
     *
     * @return mixed
     */
    public function get($key, $default = null, $fluent = false)
    {
        $option = $default;

        if (isset($this->items[$key])) {
            $option = $this->items[$key];
        }

        return ($fluent && is_array($option)) ? new static($option) : $option;
    }

    /**
     * @param string $key
     *
     * @return boolean
     */
    public function has($key)
    {
        return $this->hasKey($key);
    }
}
