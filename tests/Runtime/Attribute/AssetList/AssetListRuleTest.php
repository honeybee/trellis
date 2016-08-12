<?php

namespace Trellis\Tests\Runtime\Attribute\AssetList;

use Trellis\Runtime\Attribute\Asset\AssetRule;
use Trellis\Runtime\Attribute\AssetList\AssetListRule;
use Trellis\Runtime\Attribute\Asset\Asset;
use Trellis\Tests\TestCase;
use stdClass;

class AssetListRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new AssetListRule('assetlist', []);
        $this->assertEquals('assetlist', $rule->getName());
    }

    public function testEmptyListIsValid()
    {
        $rule = new AssetListRule('assetlist', []);
        $valid = $rule->apply([]);
        $this->assertTrue($valid);
    }

    public function testCompleteAssetDataIsValid()
    {
        $rule = new AssetListRule('assetlist', []);
        $valid = $rule->apply([
            [
                Asset::PROPERTY_LOCATION => 'foo/bar.ods',
                Asset::PROPERTY_TITLE => 'some title',
                Asset::PROPERTY_CAPTION => 'some caption',
                Asset::PROPERTY_COPYRIGHT => 'some copyright messsage',
                Asset::PROPERTY_COPYRIGHT_URL => 'http://www.example.com/foo/bar.ods',
                Asset::PROPERTY_SOURCE => 'unknown source/photographer',
                Asset::PROPERTY_METADATA => [
                    'foo' => 'foo/bar.ods'
                ]
            ]
        ]);
        $this->assertTrue($valid);
    }


    public function testMissingMandatoryLocation()
    {
        $attribute_name = 'my_assets';

        $rule = new AssetListRule('valid-my-assets', []);
        $valid = $rule->apply([
            [
                Asset::PROPERTY_TITLE => 'some title',
                Asset::PROPERTY_CAPTION => 'some caption'
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

    public function testMinimumAssetListDataIsValid()
    {
        $rule = new AssetListRule('assetlist', []);
        $valid = $rule->apply([
            [ Asset::PROPERTY_LOCATION => 'foo/bar.ods' ]
        ]);
        $this->assertTrue($valid);
    }

    public function testMinimumAssetListIsValid()
    {
        $rule = new AssetListRule('assetlist', []);
        $valid = $rule->apply([
            Asset::createFromArray([Asset::PROPERTY_LOCATION => 'asdf.ods'])
        ]);
        $this->assertTrue($valid);
    }

    public function testNullByteRemoval()
    {
        $img_data = [
            [
                Asset::PROPERTY_LOCATION => "some\x00file",
                Asset::PROPERTY_CAPTION => "some\x00file",
                Asset::PROPERTY_METADATA => [
                    'foo' => "some\x00file",
                    'aoi' => '[1,1,100,100]'
                ]
            ]
        ];

        $rule = new AssetListRule('assetlist', []);

        $valid = $rule->apply($img_data);

        $this->assertTrue($valid);

        $this->assertTrue(is_array($rule->getSanitizedValue()));

        $asset = $rule->getSanitizedValue()[0];

        $this->assertEquals("somefile", $asset->getLocation());
        $this->assertEquals("somefile", $asset->getCaption());
        $this->assertEquals("somefile", $asset->getMetadata()['foo']);
    }

    public function testDefaultRemoveNewLine()
    {
        $img_data = [
            [
                Asset::PROPERTY_LOCATION => "some\t\nfile",
            ]
        ];

        $rule = new AssetListRule('assetlist', [
            AssetRule::OPTION_LOCATION_ALLOW_CRLF => false,
            AssetRule::OPTION_LOCATION_ALLOW_TAB => false
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
        $rule = new AssetListRule('assetlist', []);
        $this->assertTrue($rule->apply($valid_value), $assert_message . ' should be accepted');
        $this->assertNotNull($rule->getSanitizedValue(), $assert_message . ' should not be null for a valid value');
    }

    public function provideValidValues()
    {
        return [
            [
                [
                    [
                        Asset::PROPERTY_LOCATION => 'some/file.ods'
                    ]
                ],
                'only 1 asset w/ only location'
            ],
            [
                [
                    [
                        Asset::PROPERTY_LOCATION => 'some/file.ods',
                        Asset::PROPERTY_COPYRIGHT_URL => 'http://example.com/some/path?q=foo#fragment'
                    ]
                ],
                '1 asset w/ location and copyright_url'
            ],
            [
                [
                    [
                        Asset::PROPERTY_LOCATION => 'some/file.ods',
                        Asset::PROPERTY_COPYRIGHT_URL => 'http://example.com/some/path?q=foo#fragment'
                    ],
                    [
                        Asset::PROPERTY_LOCATION => 'some/file.ods',
                        Asset::PROPERTY_COPYRIGHT_URL => 'http://example.com/some/path?q=foo#fragment'
                    ]
                ],
                '2 assets w/ location and copyright_url'
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testRejectionOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new AssetListRule('scalar', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be rejected');
        $this->assertNull($rule->getSanitizedValue(), print_r($rule->getSanitizedValue(), true));//$assert_message . ' should be null for an invalid value');
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
                        Asset::PROPERTY_LOCATION => 'some/file.ods',
                        Asset::PROPERTY_COPYRIGHT_URL => 'http://...example.com/some/path?q=foo#fragment'
                    ]
                ],
                'asset w/ location and copyright_url'
            ],
        ];
    }
}
