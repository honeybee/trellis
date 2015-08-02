<?php

namespace Trellis\Tests\Runtime\Attribute\GeoPoint;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\GeoPoint\GeoPoint;
use Trellis\Runtime\Attribute\GeoPoint\GeoPointAttribute;
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

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
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
            [ null ],
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
