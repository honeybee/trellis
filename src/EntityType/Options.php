<?php

namespace Trellis\EntityType;

use Trellis\Collection\Map;

class Options extends Map
{
    /**
     * @param string $key
     * @param mixed $default
     * @param bool $fluent
     *
     * @return mixed
     */
    public function get($key, $default = null, $fluent = false)
    {
        $option = array_key_exists($key, $this->items) ? $this->items[$key] : $default;

        return $fluent && is_array($option) ? new static($option) : $option;
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
