<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Text;
use Trellis\Tests\TestCase;

final class TextTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertEquals("hello world!", (new Text("hello world!"))->toNative());
        $this->assertEquals(Text::EMPTY, (new Text)->toNative());
    }

    public function testEquals(): void
    {
        $text = new Text("hello world!");
        $this->assertTrue($text->equals(new Text("hello world!")));
        $this->assertFalse($text->equals(new Text("hello universe!")));
    }
}
