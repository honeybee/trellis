<?php

namespace Trellis\Tests\Runtime\Attribute\Image;

use Trellis\Runtime\Attribute\Image\ImageRule;
use Trellis\Runtime\Attribute\Image\Image;
use Trellis\Tests\TestCase;
use stdClass;

class ImageRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new ImageRule('image', []);
        $this->assertEquals('image', $rule->getName());
    }

    public function testEmptyImageDataIsInvalid()
    {
        $rule = new ImageRule('image', []);
        $valid = $rule->apply([]);
        $this->assertFalse($valid);
    }

    public function testCompleteImageDataIsValid()
    {
        $rule = new ImageRule('image', []);
        $valid = $rule->apply(
            [
                Image::PROPERTY_LOCATION => 'foo/bar.jpg',
                Image::PROPERTY_TITLE => 'some title',
                Image::PROPERTY_CAPTION => 'some caption',
                Image::PROPERTY_COPYRIGHT => 'some copyright messsage',
                Image::PROPERTY_COPYRIGHT_URL => 'http://www.example.com/foo/bar.jpg',
                Image::PROPERTY_SOURCE => 'unknown source/photographer',
                Image::PROPERTY_METADATA => [
                    'foo' => 'foo/bar.jpg'
                ]
            ]
        );
        $this->assertTrue($valid);
    }
    public function testMinimumImageDataIsValid()
    {
        $rule = new ImageRule('image', []);
        $valid = $rule->apply([Image::PROPERTY_LOCATION => 'foo/bar.jpg']);
        $this->assertTrue($valid);
    }

    public function testMinimumImageIsValid()
    {
        $rule = new ImageRule('image', []);
        $valid = $rule->apply(Image::createFromArray([Image::PROPERTY_LOCATION => 'asdf.jpg']));
        $this->assertTrue($valid);
    }

    public function testValidImageWidthHeightConstraitsSucceed()
    {
        $rule = new ImageRule(
            'image',
            [
                ImageRule::OPTION_WIDTH_MIN_VALUE => 320,
                ImageRule::OPTION_HEIGHT_MIN_VALUE => 200,
                ImageRule::OPTION_WIDTH_MAX_VALUE => 1920,
                ImageRule::OPTION_HEIGHT_MAX_VALUE => 1080,
            ]
        );
        $valid = $rule->apply(
            Image::createFromArray(
                [
                    Image::PROPERTY_LOCATION => 'asdf.jpg',
                    Image::PROPERTY_WIDTH => 800,
                    Image::PROPERTY_HEIGHT => 600
                ]
            )
        );
        $this->assertTrue($valid);
    }

    public function testImageWidthHeightConstraitsApply()
    {
        $rule = new ImageRule(
            'image',
            [
                ImageRule::OPTION_WIDTH_MIN_VALUE => 320,
                ImageRule::OPTION_HEIGHT_MIN_VALUE => 200,
                ImageRule::OPTION_WIDTH_MAX_VALUE => 1920,
                ImageRule::OPTION_HEIGHT_MAX_VALUE => 1080,
            ]
        );
        $valid = $rule->apply(
            Image::createFromArray(
                [
                    Image::PROPERTY_LOCATION => 'asdf.jpg',
                    Image::PROPERTY_WIDTH => 800,
                    Image::PROPERTY_HEIGHT => 1600
                ]
            )
        );
        $this->assertFalse($valid);
    }

    public function testNullByteRemoval()
    {
        $img_data = [
            Image::PROPERTY_LOCATION => "some\x00file",
            Image::PROPERTY_CAPTION => "some\x00file",
            Image::PROPERTY_METADATA => [
                'foo' => "some\x00file",
                'aoi' => '[1,1,100,100]'
            ]
        ];

        $rule = new ImageRule('image', []);

        $valid = $rule->apply($img_data);

        $this->assertTrue($valid);

        $image = $rule->getSanitizedValue();

        $this->assertEquals("somefile", $image->getLocation());
        $this->assertEquals("somefile", $image->getCaption());
        $this->assertEquals("somefile", $image->getMetadata()['foo']);
    }

    public function testDefaultRemoveNewLine()
    {
        $img_data = [
            Image::PROPERTY_LOCATION => "some\t\nfile",
        ];

        $rule = new ImageRule('image', [
            ImageRule::OPTION_LOCATION_ALLOW_CRLF => false,
            ImageRule::OPTION_LOCATION_ALLOW_TAB => false
        ]);

        $valid = $rule->apply($img_data);

        $this->assertTrue($valid);
        $this->assertEquals("somefile", $rule->getSanitizedValue()->getLocation());
    }

    /**
     * @dataProvider provideValidValues
     */
    public function testAcceptanceOfValidValues($valid_value, $assert_message = '')
    {
        $rule = new ImageRule('image', []);
        $this->assertTrue($rule->apply($valid_value), $assert_message . ' should be accepted');
        $this->assertNotNull($rule->getSanitizedValue(), $assert_message . ' should not be null for a valid value');
    }

    public function provideValidValues()
    {
        return [
            [
                [
                    Image::PROPERTY_LOCATION => 'some/file.jpg'
                ],
                'image w/ only location'
            ],
            [
                [
                    Image::PROPERTY_LOCATION => 'some/file.jpg',
                    Image::PROPERTY_WIDTH => 123,
                    Image::PROPERTY_HEIGHT => 456
                ],
                'image w/ only location, width and height'
            ],
            [
                [
                    Image::PROPERTY_LOCATION => 'some/file.jpg',
                    Image::PROPERTY_COPYRIGHT_URL => 'http://example.com/some/path?q=foo#fragment'
                ],
                'image w/ location and copyright_url'
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testRejectionOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new ImageRule('scalar', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be rejected');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should be null for an invalid value');
    }

    public function provideInvalidValues()
    {
        return [
            [ new stdClass(), 'stdClass object' ],
            [ [], 'empty array' ],
            [ ['foo'], 'simple array' ],
            [ null, 'NULL' ],
            [ '', 'empty string' ],
            [ 'some string', 'simple string' ],
            [ 123, 'integer value' ],
            [ 123.456, 'float value' ],
            [ true, 'boolean TRUE' ],
            [ false, 'boolean FALSE' ],
            [ 1e12, 'float value in e-notation' ],
            [ -123, 'negative integer value' ],
            [ -345.123, 'negative float value' ],
            [
                [
                    Image::PROPERTY_LOCATION => 'some/file.jpg',
                    Image::PROPERTY_COPYRIGHT_URL => 'http://...example.com/some/path?q=foo#fragment'
                ],
                'image w/ location and copyright_url'
            ],
        ];
    }
}
