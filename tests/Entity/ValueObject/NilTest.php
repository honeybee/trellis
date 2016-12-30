<?php

namespace Trellis\Tests\Entity\ValueObject;

use Trellis\Entity\ValueObject\Nil;
use Trellis\Entity\ValueObject\Text;
use Trellis\Tests\TestCase;

final class NilTest extends TestCase
{
    /**
     * @var Nil $nil
     */
    private $nil;

    public function testToNative(): void
    {
        $this->assertNull($this->nil->toNative());
    }

    public function testEquals(): void
    {
        $this->assertTrue($this->nil->equals(new Nil));
        $this->assertFalse($this->nil->equals(new Text));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue($this->nil->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals("null", (string)$this->nil);
    }

    protected function setUp(): void
    {
        $this->nil = new Nil;
    }
}
