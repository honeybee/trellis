<?php

namespace Trellis\Error\Assert;

use Assert\Assertion as BaseAssertion;
use Trellis\Error\AssertionFailed;

final class Assertion extends BaseAssertion
{
    protected static $exceptionClass = AssertionFailed::CLASS;

    /**
     * Make a string version of a value.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected static function stringify($value)
    {
        return parent::stringify($value);
    }
}
