<?php

namespace Trellis\Tests\Runtime\Attribute\GeoPoint;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\GeoPoint\GeoPoint;
use Trellis\Runtime\Attribute\GeoPoint\GeoPointAttribute;
use Trellis\Runtime\Attribute\HasComplexValueInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use stdClass;

class GeoPointAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new GeoPointAttribute('gp', $this->getTypeMock());
        $this->assertEquals($attribute->getName(), 'gp');
        $this->assertEquals(null, $attribute->getNullValue());
        $this->assertEquals(null, $attribute->getDefaultValue());
        $this->assertInstanceOf(HasComplexValueInterface::CLASS, $attribute);
    }

    public function testValueComparison()
    {
        $gp_data = [
            GeoPoint::PROPERTY_LONGITUDE => '123',
            GeoPoint::PROPERTY_LATITUDE => 57
        ];

        $gp2_data = $gp_data;
        $gp2_data[GeoPoint::PROPERTY_LATITUDE] = 12;

        $attribute = new GeoPointAttribute('gp', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($gp_data);

        $this->assertInstanceOf(GeoPoint::CLASS, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($gp_data));
        $this->assertFalse($valueholder->sameValueAs($gp2_data));
    }

    public function testToNativeRoundtrip()
    {
        $gp_data = [
            GeoPoint::PROPERTY_LONGITUDE => 123.456,
            GeoPoint::PROPERTY_LATITUDE => 12.3456
        ];

        $native = [
            GeoPoint::PROPERTY_LONGITUDE => 123.456,
            GeoPoint::PROPERTY_LATITUDE => 12.3456
        ];

        $attribute = new GeoPointAttribute('gp', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(GeoPoint::createFromArray($gp_data));
        $this->assertInstanceOf(GeoPoint::CLASS, $valueholder->getValue());

        $this->assertEquals($native, $valueholder->toNative());
        $result = $valueholder->setValue($valueholder->toNative());
        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertInstanceOf(GeoPoint::CLASS, $valueholder->getValue());
        $this->assertEquals($native, $valueholder->toNative());
    }

    public function testToNativeRoundtripWithNullValue()
    {
        $attribute = new GeoPointAttribute('gp', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $this->assertSame($attribute->getNullValue(), $valueholder->getValue());
        $this->assertNotSame('', $valueholder->toNative());
        $this->assertNull($valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertSame($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testSettingToNullAfterValidValueWasSetWorks()
    {
        $attribute = new GeoPointAttribute('gp', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $this->assertSame($attribute->getNullValue(), $valueholder->getValue());
        $this->assertNotSame('', $valueholder->toNative());

        $point = [
            GeoPoint::PROPERTY_LONGITUDE => 123.456,
            GeoPoint::PROPERTY_LATITUDE => 12.3456
        ];

        $valueholder->setValue($point);
        $this->assertSame($point, $valueholder->getValue()->toNative());

        $valueholder->setValue(null);
        $this->assertNull($valueholder->getValue());
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->expectException(BadValueException::CLASS);
        $attribute = new GeoPointAttribute(
            'gpinvaliddefaultvalue',
            $this->getTypeMock(),
            [ GeoPointAttribute::OPTION_DEFAULT_VALUE => 5.00000001 ]
        );
        $attribute->getDefaultValue();
    }

    public function testDefaultValueWorks()
    {
        $expected = [
            GeoPoint::PROPERTY_LONGITUDE => 123.456,
            GeoPoint::PROPERTY_LATITUDE => 12.3456
        ];
        $attribute = new GeoPointAttribute(
            'def',
            $this->getTypeMock(),
            [
                GeoPointAttribute::OPTION_DEFAULT_VALUE => $expected
            ]
        );
        $def = $attribute->getDefaultValue();
        $this->assertEquals($expected, $def->toNative(), 'default value should have been set via config');
        $vh = $attribute->createValueHolder();
        $this->assertEquals($vh->getValue(), null, 'valueholder should be null as it was created w/o defval');
        $vh = $attribute->createValueHolder(true);
        $this->assertEquals($vh->getValue()->toArray(), $expected, 'valueholder should have default value');
    }

    public function testNullIslandIsUsedToSetValueBackToNull()
    {
        $attribute = new GeoPointAttribute('gp', $this->getTypeMock());
        $null_island = GeoPoint::createNullIsland();
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($null_island);
        $this->assertSame($attribute->getNullValue(), $valueholder->getValue());
        $this->assertNotSame('', $valueholder->toNative());
        $this->assertNull($valueholder->toNative());
    }

    public function testNullIslandIsAcceptedWhenConfiguredToDoSo()
    {
        $attribute = new GeoPointAttribute('gp', $this->getTypeMock(), [
            GeoPointAttribute::OPTION_NULL_ISLAND_AS_NULL => false
        ]);
        $null_island = GeoPoint::createNullIsland();
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($null_island);
        $this->assertNotSame($attribute->getNullValue(), $valueholder->getValue());
        $this->assertNotSame('', $valueholder->toNative());
        $this->assertTrue($valueholder->getValue()->isNullIsland());
    }

    /**
     * @dataProvider provideValidValues
     */
    public function testValidValue($valid_value, $assert_message = '')
    {
        $attribute = new GeoPointAttribute('validgp', $this->getTypeMock());
        $result = $attribute->getValidator()->validate($valid_value);
        $this->assertSame(IncidentInterface::SUCCESS, $result->getSeverity(), $assert_message);
    }

    public function provideValidValues()
    {
        return [
            [ null ],
            [ '' ], // treated as null value as well
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => '180',
                    GeoPoint::PROPERTY_LATITUDE => '90'
                ]
            ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => -180,
                    GeoPoint::PROPERTY_LATITUDE => -90
                ]
            ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => -180,
                    GeoPoint::PROPERTY_LATITUDE => 90
                ]
            ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => 180,
                    GeoPoint::PROPERTY_LATITUDE => -90
                ]
            ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => 0,
                    GeoPoint::PROPERTY_LATITUDE => 0
                ]
            ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => 179.99999,
                    GeoPoint::PROPERTY_LATITUDE => -89.99999
                ]
            ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => '-1.9999986',
                    GeoPoint::PROPERTY_LATITUDE => '0'
                ]
            ],
        ];
    }
    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new GeoPointAttribute('gpInvalidValue', $this->getTypeMock());
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertGreaterThanOrEqual(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return [
            [ 3.14159 ],
            [ 1337 ],
            [ 'foo' ],
            [ [] ],
            [ [180,90] ],
            [ false ],
            [ true ],
            [ new stdClass() ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => '190',
                    GeoPoint::PROPERTY_LATITUDE => '91'
                ]
            ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => 'sadf',
                    GeoPoint::PROPERTY_LATITUDE => 'asdf'
                ]
            ],
            [
                [
                    GeoPoint::PROPERTY_LONGITUDE => -181,
                    GeoPoint::PROPERTY_LATITUDE => -91
                ]
            ],
        ];
    }
}
