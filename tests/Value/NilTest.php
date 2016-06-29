<?php

namespace Trellis\Tests\Value;

use Trellis\Tests\TestCase;
use Trellis\Value\Nil;
use Trellis\Value\ValueInterface;

class NilTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Nil());
    }

    public function testToNative()
    {
        $null_value = new Nil();

        $this->assertNull($null_value->toNative());
    }
}
