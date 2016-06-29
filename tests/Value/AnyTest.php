<?php

namespace Trellis\Tests\Value;

use Trellis\Tests\TestCase;
use Trellis\Value\Any;
use Trellis\Value\ValueInterface;

class AnyTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Any('foobar'));
    }

    public function testToNative()
    {
        $any_value = new Any('foobar');

        $this->assertEquals('foobar', $any_value->toNative());
    }
}
