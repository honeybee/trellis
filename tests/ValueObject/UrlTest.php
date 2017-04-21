<?php

namespace Trellis\Tests\ValueObject;

use Trellis\Tests\TestCase;
use Trellis\ValueObject\Url;

final class UrlTest extends TestCase
{
    private const FIXED_URL = "https://www.example.com:8080/?param=value#q=trellis";

    /**
     * @var Url $url
     */
    private $url;

    public function testToNative(): void
    {
        $this->assertEquals(self::FIXED_URL, $this->url->toNative());
        $this->assertEquals("", Url::makeEmpty()->toNative());
    }

    public function testEquals(): void
    {
        $this->assertTrue($this->url->equals(Url::fromNative(self::FIXED_URL)));
        $this->assertFalse($this->url->equals(Url::fromNative("http://example.com")));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue(Url::makeEmpty()->isEmpty());
        $this->assertFalse($this->url->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals(self::FIXED_URL, (string)$this->url);
    }

    public function testGetScheme(): void
    {
        $this->assertEquals("https", $this->url->getScheme()->toNative());
    }

    public function testGetHost(): void
    {
        $this->assertEquals("www.example.com", $this->url->getHost()->toNative());
    }

    public function testGetPort(): void
    {
        $this->assertEquals(8080, $this->url->getPort()->toNative());
    }

    public function testGetPath(): void
    {
        $this->assertEquals("/", $this->url->getPath()->toNative());
    }

    public function testGetQuery(): void
    {
        $this->assertEquals("param=value", $this->url->getQuery()->toNative());
    }

    public function testGetFragment(): void
    {
        $this->assertEquals("q=trellis", $this->url->getFragment()->toNative());
    }

    protected function setUp(): void
    {
        $this->url = Url::fromNative(self::FIXED_URL);
    }
}
