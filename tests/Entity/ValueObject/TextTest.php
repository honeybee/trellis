<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Text;
use Trellis\Tests\TestCase;

final class TextTest extends TestCase
{
    const FIXED_TEXT = "hello world!";

    /**
     * @var Text $text
     */
    private $text;

    public function testToNative(): void
    {
        $this->assertEquals(self::FIXED_TEXT, $this->text->toNative());
        $this->assertEquals(Text::EMPTY, (new Text)->toNative());
    }

    public function testEquals(): void
    {
        $same_text = new Text(self::FIXED_TEXT);
        $this->assertTrue($this->text->equals($same_text));
        $different_text = new Text("hello universe!");
        $this->assertFalse($this->text->equals($different_text));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new Text)->isEmpty());
        $this->assertFalse($this->text->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals(self::FIXED_TEXT, (string)$this->text);
    }

    protected function setUp(): void
    {
        $this->text = new Text(self::FIXED_TEXT);
    }
}
