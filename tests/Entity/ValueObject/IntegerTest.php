<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Integer;
use Trellis\Tests\TestCase;

final class IntegerTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertEquals(23, (new Integer(23))->toNative());
        $this->assertEquals(Integer::EMPTY, (new Integer)->toNative());
    }

    public function testEquals(): void
    {
        $number = new Integer(23);
        $this->assertTrue($number->equals(new Integer(23)));
        $this->assertFalse($number->equals(new Integer(42)));
    }
}
