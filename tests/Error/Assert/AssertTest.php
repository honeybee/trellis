<?php

namespace Trellis\Tests\Error\Assert;

use Trellis\Error\Assert\Assert;
use Trellis\Error\AssertionFailed;
use Trellis\Error\LazyAssertionFailed;
use Trellis\Tests\TestCase;

class AssertTest extends TestCase
{
    public function testAssertionsUseCustomExceptionClass(): void
    {
        $this->setExpectedException(AssertionFailed::CLASS);
        Assert::that(null)->notEmpty();
    }

    public function testLazyAssertionsUseCustomExceptionClass(): void
    {
        $this->setExpectedException(LazyAssertionFailed::CLASS);
        Assert::lazy()
            ->that(10, 'foo')->string()
            ->that(null, 'bar')->notEmpty()
            ->that('string', 'baz')->isArray()
            ->verifyNow();
    }

    public function testLazyAssertionFailedKnowsAffectedPropertyPaths(): void
    {
        try {
            Assert::lazy()->tryAll()
                ->that(10, 'foo')->string()
                ->that(null, 'bar')->notEmpty()->string()
                ->that('string', 'baz')->isArray()
                ->verifyNow();
        } catch (LazyAssertionFailed $e) {
            $this->assertEquals(['foo', 'bar', 'baz'], $e->getAffectedPropertyPaths());
        }
    }
}
