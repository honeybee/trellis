<?php

namespace Trellis\Tests\EntityType\Attribute\Url;

use Trellis\EntityType\Attribute\Url\Url;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class UrlTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Url('http://wwww.example.com'));
    }

    public function testToNative()
    {
        $url = new Url('http://www.example.com');
        $this->assertEquals('http://www.example.com', $url->toNative());
        $url = new Url;
        $this->assertEquals('', $url->toNative());
    }

    public function testIsEmpty()
    {
        $url = new Url('http://www.example.com');
        $this->assertFalse($url->isEmpty());
        $url = new Url;
        $this->assertTrue($url->isEmpty());
        $url = new Url('');
        $this->assertTrue($url->isEmpty());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @dataProvider provideInvalidUrls
     */
    public function testInvalidUrlFormat($invalid_url)
    {
        new Url($invalid_url);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueFloat()
    {
        new Url(42.0);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new Url(23);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Url(true);
    } // @codeCoverageIgnore

    public function provideInvalidUrls()
    {
        return [
            [ 'foobar' ],
            [ 'http:/amazon.com' ],
            [ 'http//bing.de' ],
            [ 'htt://www.google.de' ],
        ];
    }
}
