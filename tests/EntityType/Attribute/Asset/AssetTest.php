<?php

namespace Trellis\Tests\EntityType\Attribute\Asset;

use Trellis\EntityType\Attribute\Asset\Asset;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class AssetTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Asset(__FILE__));
    }

    public function testToNative()
    {
        $asset = new Asset(__FILE__);
        $this->assertEquals([
            'location' => __FILE__,
            'filename' => '',
            'filesize' => null,
            'mimetype' => '',
            'source' => '',
            'title' => '',
            'caption' => '',
            'copyright' => '',
            'copyright_url' => '',
            'metadata' => []
        ], $asset->toNative());

        $asset = new Asset;
        $this->assertEquals([
            'location' => '',
            'filename' => '',
            'filesize' => null,
            'mimetype' => '',
            'source' => '',
            'title' => '',
            'caption' => '',
            'copyright' => '',
            'copyright_url' => '',
            'metadata' => []
        ], $asset->toNative());
    }

    public function testFromArray()
    {
        $data = [
            'location' => __FILE__,
            'filename' => 'foobar.pdf',
            'filesize' => 212432,
            'mimetype' => 'application/pdf',
            'source' => 'http://example.com/foobar.pdf',
            'title' => 'Example asset',
            'metadata' => [ 'external_source' => true ]
        ];

        $this->assertEquals([
            'location' => __FILE__,
            'filename' => 'foobar.pdf',
            'filesize' => 212432,
            'mimetype' => 'application/pdf',
            'source' => 'http://example.com/foobar.pdf',
            'title' => 'Example asset',
            'caption' => '',
            'copyright' => '',
            'copyright_url' => '',
            'metadata' => [ 'external_source' => true ]
        ], Asset::fromArray($data)->toNative());
    }

    public function testIsEmpty()
    {
        $asset = new Asset('hello world!');
        $this->assertFalse($asset->isEmpty());
        $asset = new Asset;
        $this->assertTrue($asset->isEmpty());
        $asset = new Asset('');
        $this->assertTrue($asset->isEmpty());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueFloat()
    {
        new Asset(42.0);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new Asset(23);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Asset(true);
    } // @codeCoverageIgnore
}
