<?php

namespace Trellis\Tests\Runtime\Attribute\Image;

use Trellis\Runtime\Attribute\Image\Image;
use Trellis\Tests\TestCase;
use InvalidArgumentException;

class ImageTest extends TestCase
{
    protected function setUp()
    {
        set_error_handler([$this, 'errorHandler']); // to catch missing argument and throw an exception for it
    }

    public function testSimpleCreateSucceeds()
    {
        $img = new Image([
            Image::PROPERTY_LOCATION => 'some/file.jpg'
        ]);
        $this->assertEquals($img->getLocation(), 'some/file.jpg');
    }

    /**
     * @expectedException Assert\InvalidArgumentException
     */
    public function testSimpleCreateFailsWithEmptyString()
    {
        $img = new Image([ Image::PROPERTY_LOCATION => '' ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateWithoutArgumentsFails()
    {
        $img = new Image([]);
    }

    public function testComplexCreateSucceeds()
    {
        $img = new Image([
            Image::PROPERTY_LOCATION => 'some/file.jpg',
            Image::PROPERTY_TITLE => 'title',
            Image::PROPERTY_CAPTION => 'caption',
            Image::PROPERTY_COPYRIGHT => 'copyright',
            Image::PROPERTY_COPYRIGHT_URL => 'copyright_url',
            Image::PROPERTY_SOURCE => 'source',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => 1337
            ]
        ]);

        $this->assertEquals('some/file.jpg', $img->getLocation());
        $this->assertEquals('title', $img->getTitle());
        $this->assertEquals('caption', $img->getCaption());
        $this->assertEquals('copyright', $img->getCopyright());
        $this->assertEquals('copyright_url', $img->getCopyrightUrl());
        $this->assertEquals('source', $img->getSource());
        $this->assertEquals(['foo' => 'bar', 'leet' => 1337], $img->getMetaData());
    }

    public function testCreateFromPartialArraySucceeds()
    {
        $img = Image::createFromArray([
            Image::PROPERTY_LOCATION => 'some/file.jpg',
            Image::PROPERTY_TITLE => 'title',
            Image::PROPERTY_SOURCE => 'source',
            Image::PROPERTY_META_DATA => [
                'foo' => 'bar',
                'leet' => 1337
            ]
        ]);

        $this->assertEquals('some/file.jpg', $img->getLocation());
        $this->assertEquals('title', $img->getTitle());
        $this->assertEquals('source', $img->getSource());
        $this->assertEquals(['foo' => 'bar', 'leet' => 1337], $img->getMetaData());
    }

    public function testComparisonOfTwoSimilarImagesSucceeds()
    {
        $other_img = new Image([
            Image::PROPERTY_LOCATION => 'some/other.png',
            Image::PROPERTY_TITLE => 'other_title'
        ]);

        $img = Image::createFromArray($other_img->toNative());

        $this->assertEquals('some/other.png', $img->getLocation());
        $this->assertEquals('other_title', $img->getTitle());

        $this->assertTrue($img->similarTo($other_img));
    }

    public function testCreateWithSucceeds()
    {
        $img = new Image([
            Image::PROPERTY_LOCATION => 'some/other.png',
            Image::PROPERTY_TITLE => 'other_title'
        ]);

        $diff_img = $img->createWith([
            Image::PROPERTY_LOCATION => 'omgomgomg'
        ]);

        $this->assertEquals('omgomgomg', $diff_img->getLocation());
        $this->assertEquals('other_title', $diff_img->getTitle());
        $this->assertFalse($img->similarTo($diff_img));
        $this->assertFalse($diff_img->similarTo($img));
    }

    public function testToArrayValuesEqualToNative()
    {
        $img = new Image([
            Image::PROPERTY_LOCATION => 'some/other.png',
            Image::PROPERTY_TITLE => 'other_title'
        ]);

        $a = $img->toArray();
        $b = $img->toNative();

        $this->assertEquals($a, $b);
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        throw new InvalidArgumentException(
            sprintf(
                'Missing argument. %s %s %s %s',
                $errno,
                $errstr,
                $errfile,
                $errline
            )
        );
    }
}
