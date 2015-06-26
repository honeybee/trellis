<?php

namespace Trellis\Common;

use Trellis\Common\ConfigurableInterface;
use Trellis\Common\Object;
use Trellis\Common\Options;
use Trellis\Common\OptionsInterface;

class Configurable extends Object implements ConfigurableInterface
{
    /**
     * @var Options $options
     */
    protected $options;

    /**
     * Override the Object's constructor to always initialize options.
     * To set options on this instance use the special key 'options'.
     *
     * @param array $state data to set on the object (key-value pairs)
     */
    public function __construct(array $state = [])
    {
        parent::__construct($state);

        if (!$this->options instanceof OptionsInterface) {
            $this->options = new Options();
        }
    }

    /**
     * Returns whether the option exists or not.
     *
     * @param mixed $key key to check
     *
     * @return bool true, if key exists; false otherwise
     */
    public function hasOption($key)
    {
        return $this->options->has($key);
    }

    /**
     * Returns the value for the given key.
     *
     * @param mixed $key key to get value of
     * @param mixed $default value to return if key doesn't exist
     *
     * @return mixed value for that key or default given
     */
    public function getOption($key, $default = null)
    {
        return $this->options->get($key, $default);
    }

    /**
     * Allows to search for specific data values via JMESPath expressions.
     *
     * Some example expressions as a quick start:
     *
     * - "nested.key"           returns the value of the nested "key"
     * - "nested.*"             returns all values available under the "nested" key
     * - "*.key"                returns all values of "key"s on any second level array
     * - "[key, nested.key]"    returns first level "key" value and the first value of the "nested" key array
     * - "[key, nested[0]"      returns first level "key" value and the first value of the "nested" key array
     * - "nested.key || key"    returns the value of the first matching expression
     *
     * @see http://jmespath.readthedocs.org/en/latest/ and https://github.com/mtdowling/jmespath.php
     *
     * @param string $expression JMESPath expression to evaluate on stored data
     *
     * @return mixed|null data in various types (scalar, array etc.) depending on the found results
     *
     * @throws \JmesPath\SyntaxErrorException on invalid expression syntax
     * @throws \RuntimeException e.g. if JMESPath cache directory cannot be written
     * @throws \InvalidArgumentException e.g. if JMESPath builtin functions can't be called
     */
    public function getOptionValues($expression = '*')
    {
        return $this->options->getValues($expression);
    }

    /**
     * Returns the data as an associative array.
     *
     * @return array with all data
     */
    public function getOptionsAsArray()
    {
        return $this->options->toArray();
    }

    /**
     * Return this object's immutable options instance.
     *
     * @return Options instance used internally
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the given options.
     *
     * @param array $options options to set
     */
    protected function setOptions($options)
    {
        if (!$options instanceof OptionsInterface) {
            $options = is_array($options) ? $options : [];
            $options = new Options($options);
        }

        $this->options = $options;
    }
}
