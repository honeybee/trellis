<?php

namespace Trellis\Assert;

use Assert\Assert as BaseAssert;
use Trellis\Error\LazyAssertionFailed;

abstract class Assert extends BaseAssert
{
    /**
     * @var string
     */
    protected static $lazyAssertionExceptionClass = LazyAssertionFailed::class;

    /**
     * @var string
     */
    protected static $assertionClass = Assertion::class;
}
