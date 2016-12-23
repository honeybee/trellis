<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Decimal;
use Trellis\Tests\TestCase;

final class DecimalTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertEquals(2.3, (new Decimal(2.3))->toNative());
        $this->assertEquals(Decimal::EMPTY, (new Decimal)->toNative());
    }

    public function testEquals(): void
    {
        $bool = new Decimal(2.3);
        $this->assertTrue($bool->equals(new Decimal(2.3)));
        $this->assertFalse($bool->equals(new Decimal(4.2)));
    }
}
