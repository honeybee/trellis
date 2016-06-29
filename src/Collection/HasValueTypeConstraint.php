<?php

namespace Trellis\Collection;

use Trellis\Exception;

trait HasValueTypeConstraint
{
    protected static $native_types = [ 'string', 'bool', 'int', 'float', 'array', 'scalar' ];

    protected $value_type;

    public function __construct($value_type, array $values = [])
    {
        $this->value_type = $value_type;

        parent::__construct($values);
    }

    protected function guardConstraints(array $values)
    {
        parent::guardConstraints($values);

        $value_type = $this->getValueType();
        foreach ($values as $value) {
            if (in_array($value_type, self::$native_types)) {
                $this->assertIsNativeValue($value_type, $value);
            } else {
                $this->assertIsInstanceOf($value_type, $value);
            }
        }
    }

    protected function getValueType()
    {
        return $this->value_type;
    }

    protected function assertIsInstanceOf($class, $value)
    {
        if (!$value instanceof $class) {
            throw new Exception(
                sprintf(
                    "Values passed to the '%s' method must relate to '%s'. An instance of '%s' was given instead.",
                    __METHOD__,
                    $class,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }
    }

    protected function assertIsNativeValue($expected_type, $value)
    {
        $value_type = is_object($value) ? get_class($value) : gettype($value);
        $error_tpl = "Values passed to the '%s' must be '%s', instanceof '%s' given instead.";
        $violation = false;
        switch ($expected_type) {
            case 'string':
                if (!is_string($value)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $value_type);
                }
                break;

            case 'int':
                if (!is_int($value)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $value_type);
                }
                break;
            case 'float':
                if (!is_float($value)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $value_type);
                }
                break;
            case 'boolean':
                if (!is_float($value)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $value_type);
                }
                break;
            case 'array':
                if (!is_array($value)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $value_type);
                }
                break;
            default:
                if (!is_scalar($value)) {
                    $violation = sprintf($error_tpl, __METHOD__, 'scalar', $value_type);
                }
                break;
        }

        if ($violation) {
            throw new Exception($violation);
        }
    }
}
