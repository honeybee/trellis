<?php

namespace Trellis\Tests\Runtime\Attribute\AssetList;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\AssetList\AssetListAttribute;
use Trellis\Runtime\Attribute\Asset\Asset;
use Trellis\Runtime\Attribute\Asset\AssetRule;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use stdClass;

class AssetListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new AssetListAttribute('assetlist', $this->getTypeMock());
        $this->assertEquals($attribute->getName(), 'assetlist');
        $this->assertEquals([], $attribute->getNullValue());
        $this->assertEquals([], $attribute->getDefaultValue());
    }

    public function testValueComparison()
    {
        $asset_data = [
            Asset::PROPERTY_LOCATION => 'some.ods',
            Asset::PROPERTY_COPYRIGHT => 'some copyright string',
            Asset::PROPERTY_METADATA => [
                'foo' => 'bar',
                'leet' => 1337,
                'bool' => true
            ]
        ];

        $asset2_data = $asset_data;
        $asset2_data[Asset::PROPERTY_SOURCE] = 'some source';

        $asset_list_data = [
            $asset_data,
            $asset2_data,
        ];

        $expected_list = [
            Asset::createFromArray($asset_data),
            Asset::createFromArray($asset2_data),
        ];

        $asset3_data = $asset2_data;
        $asset3_data[Asset::PROPERTY_SOURCE] = 'some other source';

        $expected_other_list = [
            Asset::createFromArray($asset_data),
            Asset::createFromArray($asset3_data),
        ];

        $attribute = new AssetListAttribute('assetlist', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($asset_list_data);

        $this->assertInstanceOf(Asset::CLASS, $valueholder->getValue()[0]);
        $this->assertInstanceOf(Asset::CLASS, $valueholder->getValue()[1]);

        $this->assertTrue($valueholder->sameValueAs($expected_list));
        $this->assertFalse($valueholder->sameValueAs($expected_other_list));
    }

    public function testMetadataValuesAreIntegerOnlyIfConfigured()
    {
        $asset_data = [
            [
                Asset::PROPERTY_LOCATION => 'some.ods',
                Asset::PROPERTY_METADATA => [
                    'leet' => 1337,
                    'foo' => -1337,
                ]
            ]
        ];
        $expected = $asset_data;

        $attribute = new AssetListAttribute(
            'assetlist',
            $this->getTypeMock(),
            [ AssetRule::OPTION_METADATA_VALUE_TYPE => AssetRule::METADATA_VALUE_TYPE_INTEGER ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($asset_data);

        $this->assertInstanceOf(Asset::CLASS, $valueholder->getValue()[0]);
        $this->assertTrue($valueholder->sameValueAs($expected));
    }

    public function testRejectNonIntegerMetadataValuesIfConfigured()
    {
        $asset_data = [
            [
                Asset::PROPERTY_LOCATION => 'some.ods',
                Asset::PROPERTY_METADATA => [
                    'foo' => 'bar',
                    'leet' => 1337,
                    'bool' => true
                ]
            ]
        ];

        $attribute = new AssetListAttribute(
            'assetlist',
            $this->getTypeMock(),
            [ AssetRule::OPTION_METADATA_VALUE_TYPE => AssetRule::METADATA_VALUE_TYPE_INTEGER ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($asset_data);
        $this->assertEmpty($valueholder->getValue());
    }

    public function testToNativeRoundtrip()
    {
        $asset_list_data = [
            [
                Asset::PROPERTY_LOCATION => 'some.ods',
                Asset::PROPERTY_COPYRIGHT => 'some copyright string',
                Asset::PROPERTY_FILENAME => '.hid|den.ods',
                Asset::PROPERTY_METADATA => [
                    'foo' => 'bar',
                    'leet' => 1337,
                    'bool' => true
                ]
            ]
        ];

        $native = [
            [
                Asset::PROPERTY_LOCATION => 'some.ods',
                Asset::PROPERTY_TITLE => '',
                Asset::PROPERTY_CAPTION => '',
                Asset::PROPERTY_COPYRIGHT => 'some copyright string',
                Asset::PROPERTY_COPYRIGHT_URL => '',
                Asset::PROPERTY_FILESIZE => 0,
                Asset::PROPERTY_FILENAME => 'hid-den.ods',
                Asset::PROPERTY_MIMETYPE => '',
                Asset::PROPERTY_SOURCE => '',
                Asset::PROPERTY_METADATA => [
                    'foo' => 'bar',
                    'leet' => 1337,
                    'bool' => true
                ]
            ]
        ];

        $attribute = new AssetListAttribute('assetlist', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($asset_list_data);

        $assets = $valueholder->getValue();

        $this->assertTrue(is_array($assets));
        $this->assertInstanceOf(Asset::CLASS, $assets[0]);
        $this->assertEquals($native, $valueholder->toNative());

        $result = $valueholder->setValue($valueholder->toNative());

        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertInstanceOf(Asset::CLASS, $valueholder->getValue()[0]);
        $this->assertEquals('some.ods', $valueholder->getValue()[0]->getLocation());
        $this->assertEquals($native, $valueholder->toNative());
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new AssetListAttribute(
            'assetinvaliddefaultvalue',
            $this->getTypeMock(),
            [ AssetListAttribute::OPTION_DEFAULT_VALUE => 'trololo' ]
        );
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new AssetListAttribute('assetlistwithInvalidValue', $this->getTypeMock());
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
                        Asset::PROPERTY_LOCATION => 'sadf.ods',
                        Asset::PROPERTY_COPYRIGHT_URL => 'localhost'
                    ]
                ]
            ],
            [
                [
                    [
                        Asset::PROPERTY_LOCATION => 'sadf.ods',
                        Asset::PROPERTY_COPYRIGHT_URL => 'http://..com'
                    ]
                ]
            ],
            [
                [
                    [
                        Asset::PROPERTY_LOCATION => 'sadf.ods',
                        Asset::PROPERTY_COPYRIGHT_URL => 'javascript:alert(1)'
                    ]
                ]
            ],
        ];
    }
}
