<?php

namespace Trellis\Collection;

use Trellis\Exception;

trait ItemTypeConstraint
{
    protected static $native_types = [ 'string', 'bool', 'int', 'float', 'array', 'scalar' ];

    protected $item_type;

    public function __construct($item_type, array $items = [])
    {
        $this->item_type = $item_type;

        parent::__construct($items);
    }

    protected function guardConstraints(array $items)
    {
        parent::guardConstraints($items);

        $item_type = $this->getItemType();
        foreach ($items as $item) {
            if (in_array($item_type, self::$native_types)) {
                $this->assertIsNativeValue($item_type, $item);
            } else {
                $this->assertIsInstanceOf($item_type, $item);
            }
        }
    }

    protected function getItemType()
    {
        return $this->item_type;
    }

    protected function assertIsInstanceOf($class, $item)
    {
        if (!$item instanceof $class) {
            throw new Exception(
                sprintf(
                    "Values passed to the '%s' method must relate to '%s'. An instance of '%s' was given instead.",
                    __METHOD__,
                    $class,
                    is_object($item) ? get_class($item) : gettype($item)
                )
            );
        }
    }

    protected function assertIsNativeValue($expected_type, $item)
    {
        $item_type = is_object($item) ? get_class($item) : gettype($item);
        $error_tpl = "Values passed to the '%s' must be '%s', instanceof '%s' given instead.";
        $violation = false;
        switch ($expected_type) {
            case 'string':
                if (!is_string($item)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $item_type);
                }
                break;

            case 'int':
                if (!is_int($item)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $item_type);
                }
                break;
            case 'float':
                if (!is_float($item)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $item_type);
                }
                break;
            case 'boolean':
                if (!is_float($item)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $item_type);
                }
                break;
            case 'array':
                if (!is_array($item)) {
                    $violation = sprintf($error_tpl, __METHOD__, $expected_type, $item_type);
                }
                break;
            default:
                if (!is_scalar($item)) {
                    $violation = sprintf($error_tpl, __METHOD__, 'scalar', $item_type);
                }
                break;
        }

        if ($violation) {
            throw new Exception($violation);
        }
    }
}
