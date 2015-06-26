<?php

namespace Trellis\Tests\Runtime\Attribute\Asset;

use Trellis\Runtime\Attribute\Asset\Asset;
use Trellis\Runtime\Attribute\Asset\AssetRule;
use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Trellis\Tests\TestCase;
use stdClass;

class AssetRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new AssetRule('image', []);
        $this->assertEquals('image', $rule->getName());
    }

    public function testEmptyAssetDataIsInvalid()
    {
        $rule = new AssetRule('image', []);
        $valid = $rule->apply([]);
        $this->assertFalse($valid);
    }

    public function testCompleteAssetDataIsValid()
    {
        $rule = new AssetRule('image', []);
        $valid = $rule->apply(
            [
                Asset::PROPERTY_LOCATION => 'foo/bar.jpg',
                Asset::PROPERTY_TITLE => 'some title',
                Asset::PROPERTY_CAPTION => 'some caption',
                Asset::PROPERTY_COPYRIGHT => 'some copyright messsage',
                Asset::PROPERTY_COPYRIGHT_URL => 'http://www.example.com/foo/bar.jpg',
                Asset::PROPERTY_SOURCE => 'unknown source/photographer',
                Asset::PROPERTY_META_DATA => [
                    'foo' => 'foo/bar.jpg'
                ]
            ]
        );
        $this->assertTrue($valid);
    }
    public function testMinimumAssetDataIsValid()
    {
        $rule = new AssetRule('image', []);
        $valid = $rule->apply([Asset::PROPERTY_LOCATION => 'foo/bar.jpg']);
        $this->assertTrue($valid);
    }

    public function testMinimumAssetIsValid()
    {
        $rule = new AssetRule('image', []);
        $valid = $rule->apply(Asset::createFromArray([Asset::PROPERTY_LOCATION => 'asdf.jpg']));
        $this->assertTrue($valid);
    }

    public function testNullByteRemoval()
    {
        $img_data = [
            Asset::PROPERTY_LOCATION => "some\x00file",
            Asset::PROPERTY_CAPTION => "some\x00file",
            Asset::PROPERTY_META_DATA => [
                'foo' => "some\x00file",
                'aoi' => '[1,1,100,100]'
            ]
        ];

        $rule = new AssetRule('image', []);

        $valid = $rule->apply($img_data);

        $this->assertTrue($valid);

        $image = $rule->getSanitizedValue();

        $this->assertEquals("somefile", $image->getLocation());
        $this->assertEquals("somefile", $image->getCaption());
        $this->assertEquals("somefile", $image->getMetaData()['foo']);
    }

    public function testDefaultRemoveNewLine()
    {
        $img_data = [
            Asset::PROPERTY_LOCATION => "some\t\nfile",
        ];

        $rule = new AssetRule('image', [
            'location_' . TextRule::OPTION_ALLOW_CRLF => false,
            'location_' . TextRule::OPTION_ALLOW_TAB => false
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
        $rule = new AssetRule('image', []);
        $this->assertTrue($rule->apply($valid_value), $assert_message . ' should be accepted');
        $this->assertNotNull($rule->getSanitizedValue(), $assert_message . ' should not be null for a valid value');
    }

    public function provideValidValues()
    {
        return [
            [
                [
                    Asset::PROPERTY_LOCATION => 'some/file.jpg'
                ],
                'image w/ only location'
            ],
            [
                [
                    Asset::PROPERTY_LOCATION => 'some/file.jpg',
                    Asset::PROPERTY_COPYRIGHT_URL => 'http://example.com/some/path?q=foo#fragment'
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
        $rule = new AssetRule('scalar', []);
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
                    Asset::PROPERTY_LOCATION => 'some/file.jpg',
                    Asset::PROPERTY_COPYRIGHT_URL => 'http://...example.com/some/path?q=foo#fragment'
                ],
                'image w/ location and copyright_url'
            ],
        ];
    }
}
