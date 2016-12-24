<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Url;
use Trellis\Tests\TestCase;

final class UrlTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertEquals("http://www.google.de/", (new Url("http://www.google.de"))->toNative());
        $this->assertEquals(Url::EMPTY, (new Url)->toNative());
    }

    public function testEquals(): void
    {
        $text = new Url("http://www.google.de");
        $this->assertTrue($text->equals(new Url("http://www.google.de/")));
        $this->assertFalse($text->equals(new Url("http://www.bing.com")));
    }
}
