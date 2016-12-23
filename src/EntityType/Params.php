<?php

namespace Trellis\EntityType;

class Params
{
    /**
     * @param mixed[] $params
     */
    private $params = [];

    /**
     * @param mixed[] $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @param string $param_name
     * @param bool $treat_name_as_path
     *
     * @return mixed
     */
    public function get(string $param_name, bool $treat_name_as_path = true)
    {
        if (!$treat_name_as_path) {
            return $this->has($param_name) ? $this->params[$param_name] : null;
        }
        $params = $this->params;
        $name_parts = array_reverse(explode('.', $param_name));
        $cur_val = &$params;
        while (count($name_parts) > 1 && $cur_name = array_pop($name_parts)) {
            if (!array_key_exists($cur_name, $cur_val)) {
                return null;
            }
            $cur_val = &$cur_val[$cur_name];
        }
        return array_key_exists($name_parts[0], $cur_val) ? $cur_val[$name_parts[0]] : null;
    }

    /**
     * @param string $param_name
     *
     * @return bool
     */
    public function has(string $param_name): bool
    {
        return array_key_exists($param_name, $this->params);
    }
}
