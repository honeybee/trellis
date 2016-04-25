<?php

namespace Trellis\Tests\Runtime\Attribute\ImageList;

use Trellis\Runtime\Attribute\Image\ImageRule;
use Trellis\Runtime\Attribute\ImageList\ImageListRule;
use Trellis\Runtime\Attribute\Image\Image;
use Trellis\Tests\TestCase;
use stdClass;

class ImageListRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new ImageListRule('imagelist', []);
        $this->assertEquals('imagelist', $rule->getName());
    }

    public function testEmptyListIsValid()
    {
        $rule = new ImageListRule('imagelist', []);
        $valid = $rule->apply([]);
        $this->assertTrue($valid);
    }

    public function testCompleteImageDataIsValid()
    {
        $rule = new ImageListRule('imagelist', []);
        $valid = $rule->apply([
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
        ]);
        $this->assertTrue($valid);
    }


    public function testMissingMandatoryLocation()
    {
        $attribute_name = 'my_images';

        $rule = new ImageListRule('valid-my-images', []);
        $valid = $rule->apply([
            [
                Image::PROPERTY_TITLE => 'some title',
                Image::PROPERTY_CAPTION => 'some caption'
            ]
        ]);

        $error_parts = [ $attribute_name ];
        foreach ($rule->getIncidents() as $name => $incident) {
            $error_parts[] = $name;
            $incident_params = $incident->getParameters();
            if (isset($incident_params['path_parts'])) {
                foreach (array_reverse($incident_params['path_parts']) as $incident_path_part) {
                    $error_parts[] = $incident_path_part;
                }
            }
        }

        $expected_parts = [ $attribute_name, 'invalid_data', 0];
        $this->assertEquals($expected_parts, $error_parts);
    }

    public function testMinimumImageListDataIsValid()
    {
        $rule = new ImageListRule('imagelist', []);
        $valid = $rule->apply([
            [ Image::PROPERTY_LOCATION => 'foo/bar.jpg' ]
        ]);
        $this->assertTrue($valid);
    }

    public function testMinimumImageListIsValid()
    {
        $rule = new ImageListRule('imagelist', []);
        $valid = $rule->apply([
            Image::createFromArray([Image::PROPERTY_LOCATION => 'asdf.jpg'])
        ]);
        $this->assertTrue($valid);
    }

    public function testNullByteRemoval()
    {
        $img_data = [
            [
                Image::PROPERTY_LOCATION => "some\x00file",
                Image::PROPERTY_CAPTION => "some\x00file",
                Image::PROPERTY_METADATA => [
                    'foo' => "some\x00file",
                    'aoi' => '[1,1,100,100]'
                ]
            ]
        ];

        $rule = new ImageListRule('imagelist', []);

        $valid = $rule->apply($img_data);

        $this->assertTrue($valid);

        $this->assertTrue(is_array($rule->getSanitizedValue()));

        $image = $rule->getSanitizedValue()[0];

        $this->assertEquals("somefile", $image->getLocation());
        $this->assertEquals("somefile", $image->getCaption());
        $this->assertEquals("somefile", $image->getMetadata()['foo']);
    }

    public function testDefaultRemoveNewLine()
    {
        $img_data = [
            [
                Image::PROPERTY_LOCATION => "some\t\nfile",
            ]
        ];

        $rule = new ImageListRule('imagelist', [
            ImageRule::OPTION_LOCATION_ALLOW_CRLF => false,
            ImageRule::OPTION_LOCATION_ALLOW_TAB => false
        ]);

        $valid = $rule->apply($img_data);

        $this->assertTrue($valid);
        $this->assertEquals("somefile", $rule->getSanitizedValue()[0]->getLocation());
    }

    /**
     * @dataProvider provideValidValues
     */
    public function testAcceptanceOfValidValues($valid_value, $assert_message = '')
    {
        $rule = new ImageListRule('imagelist', []);
        $this->assertTrue($rule->apply($valid_value), $assert_message . ' should be accepted');
        $this->assertNotNull($rule->getSanitizedValue(), $assert_message . ' should not be null for a valid value');
    }

    public function provideValidValues()
    {
        return [
            [
                [
                    [
                        Image::PROPERTY_LOCATION => 'some/file.jpg'
                    ]
                ],
                'only 1 image w/ only location'
            ],
            [
                [
                    [
                        Image::PROPERTY_LOCATION => 'some/file.jpg',
                        Image::PROPERTY_COPYRIGHT_URL => 'http://example.com/some/path?q=foo#fragment'
                    ]
                ],
                '1 image w/ location and copyright_url'
            ],
            [
                [
                    [
                        Image::PROPERTY_LOCATION => 'some/file.jpg',
                        Image::PROPERTY_COPYRIGHT_URL => 'http://example.com/some/path?q=foo#fragment'
                    ],
                    [
                        Image::PROPERTY_LOCATION => 'some/file.jpg',
                        Image::PROPERTY_COPYRIGHT_URL => 'http://example.com/some/path?q=foo#fragment'
                    ]
                ],
                '2 images w/ location and copyright_url'
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testRejectionOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new ImageListRule('scalar', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be rejected');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should be null for an invalid value');
    }

    public function provideInvalidValues()
    {
        return [
            [ new stdClass(), 'stdClass object' ],
            [ [[]], 'empty array in list' ],
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
                    [
                        Image::PROPERTY_LOCATION => 'some/file.jpg',
                        Image::PROPERTY_COPYRIGHT_URL => 'http://...example.com/some/path?q=foo#fragment'
                    ]
                ],
                'image w/ location and copyright_url'
            ],
        ];
    }
}
