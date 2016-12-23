<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Boolean;
use Trellis\Tests\TestCase;

final class BooleanTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertTrue((new Boolean(true))->toNative());
        $this->assertFalse((new Boolean(false))->toNative());
        $this->assertEquals(Boolean::EMPTY, (new Boolean)->toNative());
    }

    public function testEquals(): void
    {
        $bool = new Boolean(true);
        $this->assertTrue($bool->equals(new Boolean(true)));
        $this->assertFalse($bool->equals(new Boolean(false)));
    }
}
