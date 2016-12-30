<?php

namespace Trellis\Tests\Assert;

use Trellis\Assert\Assert;
use Trellis\Error\LazyAssertionFailed;
use Trellis\Tests\TestCase;

final class AssertTest extends TestCase
{
    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testAssertionsUseCustomExceptionClass(): void
    {
        Assert::that(null)->notEmpty();
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\LazyAssertionFailed
     */
    public function testLazyAssertionsUseCustomExceptionClass(): void
    {
        Assert::lazy()
            ->that(10, "foo")->string()
            ->that(null, "bar")->notEmpty()
            ->that("string", "baz")->isArray()
            ->verifyNow();
    } // @codeCoverageIgnore

    public function testLazyAssertionFailedKnowsAffectedPropertyPaths(): void
    {
        try {
            Assert::lazy()->tryAll()
                ->that(10, "foo")->string()
                ->that(null, "bar")->notEmpty()->string()
                ->that("string", "baz")->isArray()
                ->verifyNow();
        } catch (LazyAssertionFailed $e) {
            $this->assertEquals(["foo", "bar", "baz"], $e->getPropertyPaths());
        }
    }
}
