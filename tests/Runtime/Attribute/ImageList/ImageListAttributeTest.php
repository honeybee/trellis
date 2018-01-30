<?php

namespace Trellis\Tests\Runtime\Attribute\ImageList;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\ImageList\ImageListAttribute;
use Trellis\Runtime\Attribute\Image\Image;
use Trellis\Runtime\Attribute\Image\ImageRule;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use stdClass;

class ImageListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new ImageListAttribute('imagelist', $this->getTypeMock());
        $this->assertEquals($attribute->getName(), 'imagelist');
        $this->assertEquals([], $attribute->getNullValue());
        $this->assertEquals([], $attribute->getDefaultValue());
    }

    public function testValueComparison()
    {
        $img_data = [
            Image::PROPERTY_LOCATION => 'some.jpg',
            Image::PROPERTY_COPYRIGHT => 'some copyright string',
            Image::PROPERTY_METADATA => [
                'foo' => 'bar',
                'leet' => 1337,
                'bool' => true
            ]
        ];

        $img2_data = $img_data;
        $img2_data[Image::PROPERTY_SOURCE] = 'some source';

        $img_list_data = [
            $img_data,
            $img2_data,
        ];

        $expected_list = [
            Image::createFromArray($img_data),
            Image::createFromArray($img2_data),
        ];

        $img3_data = $img2_data;
        $img3_data[Image::PROPERTY_SOURCE] = 'some other source';

        $expected_other_list = [
            Image::createFromArray($img_data),
            Image::createFromArray($img3_data),
        ];

        $attribute = new ImageListAttribute('imagelist', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($img_list_data);

        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue()[0]);
        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue()[1]);

        $this->assertTrue($valueholder->sameValueAs($expected_list));
        $this->assertFalse($valueholder->sameValueAs($expected_other_list));
    }

    public function testMetadataValuesAreIntegerOnlyIfConfigured()
    {
        $img_data = [
            [
                Image::PROPERTY_LOCATION => 'some.jpg',
                Image::PROPERTY_METADATA => [
                    'leet' => 1337,
                    'foo' => -1337,
                ]
            ]
        ];
        $expected = $img_data;

        $attribute = new ImageListAttribute(
            'imagelist',
            $this->getTypeMock(),
            [ ImageRule::OPTION_METADATA_VALUE_TYPE => ImageRule::METADATA_VALUE_TYPE_INTEGER ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($img_data);

        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue()[0]);
        $this->assertTrue($valueholder->sameValueAs($expected));
    }

    public function testRejectNonIntegerMetadataValuesIfConfigured()
    {
        $img_data = [
            [
                Image::PROPERTY_LOCATION => 'some.jpg',
                Image::PROPERTY_METADATA => [
                    'foo' => 'bar',
                    'leet' => 1337,
                    'bool' => true
                ]
            ]
        ];

        $attribute = new ImageListAttribute(
            'imagelist',
            $this->getTypeMock(),
            [ ImageRule::OPTION_METADATA_VALUE_TYPE => ImageRule::METADATA_VALUE_TYPE_INTEGER ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($img_data);
        $this->assertEmpty($valueholder->getValue());
    }

    public function testToNativeRoundtrip()
    {
        $img_list_data = [
            [
                Image::PROPERTY_LOCATION => 'some.jpg',
                Image::PROPERTY_COPYRIGHT => 'some copyright string',
                Image::PROPERTY_AOI => '[12,123,23, 235]',
                Image::PROPERTY_WIDTH => 320,
                Image::PROPERTY_FILENAME => '.hid|den.jpg',
                Image::PROPERTY_METADATA => [
                    'foo' => 'bar',
                    'leet' => 1337,
                    'bool' => true
                ]
            ]
        ];

        $native = [
            [
                Image::PROPERTY_LOCATION => 'some.jpg',
                Image::PROPERTY_TITLE => '',
                Image::PROPERTY_CAPTION => '',
                Image::PROPERTY_COPYRIGHT => 'some copyright string',
                Image::PROPERTY_COPYRIGHT_URL => '',
                Image::PROPERTY_WIDTH => 320,
                Image::PROPERTY_HEIGHT => 0,
                Image::PROPERTY_FILESIZE => 0,
                Image::PROPERTY_FILENAME => 'hid-den.jpg',
                Image::PROPERTY_MIMETYPE => '',
                Image::PROPERTY_AOI => '[12,123,23, 235]',
                Image::PROPERTY_SOURCE => '',
                Image::PROPERTY_METADATA => [
                    'foo' => 'bar',
                    'leet' => 1337,
                    'bool' => true
                ]
            ]
        ];

        $attribute = new ImageListAttribute('imagelist', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($img_list_data);

        $images = $valueholder->getValue();

        $this->assertTrue(is_array($images));
        $this->assertInstanceOf(Image::CLASS, $images[0]);
        $this->assertEquals($native, $valueholder->toNative());

        $result = $valueholder->setValue($valueholder->toNative());

        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertInstanceOf(Image::CLASS, $valueholder->getValue()[0]);
        $this->assertEquals('some.jpg', $valueholder->getValue()[0]->getLocation());
        $this->assertEquals($native, $valueholder->toNative());
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->expectException(BadValueException::CLASS);
        $attribute = new ImageListAttribute(
            'imageinvaliddefaultvalue',
            $this->getTypeMock(),
            [ ImageListAttribute::OPTION_DEFAULT_VALUE => 'trololo' ]
        );
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new ImageListAttribute('imagelistwithInvalidValue', $this->getTypeMock());
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertGreaterThanOrEqual(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return [
            [ null ],
            [ 3.14159 ],
            [ 1337 ],
            [ 'foo' ],
            [ [[]] ],
            [ false ],
            [ true ],
            [ new stdClass() ],
            [
                [
                    [
                        Image::PROPERTY_LOCATION => 'sadf.jpg',
                        Image::PROPERTY_COPYRIGHT_URL => 'localhost'
                    ]
                ]
            ],
            [
                [
                    [
                        Image::PROPERTY_LOCATION => 'sadf.jpg',
                        Image::PROPERTY_COPYRIGHT_URL => 'http://..com'
                    ]
                ]
            ],
            [
                [
                    [
                        Image::PROPERTY_LOCATION => 'sadf.jpg',
                        Image::PROPERTY_COPYRIGHT_URL => 'javascript:alert(1)'
                    ]
                ]
            ],
        ];
    }
}
