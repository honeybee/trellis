<?php

namespace Trellis\Tests\Runtime\Attribute\Integer;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\Integer\IntegerAttribute;
use Trellis\Runtime\Attribute\Integer\IntegerValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use stdClass;

class IntegerAttributeTest extends TestCase
{
    const ATTR_NAME = 'Integer';

    public function testCreate()
    {
        $attribute = new IntegerAttribute(self::ATTR_NAME, $this->getTypeMock());
        $this->assertEquals($attribute->getName(), self::ATTR_NAME);
        $this->assertEquals(0, $attribute->getNullValue());
    }

    public function testCreateValueWithDefaultValues()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_DEFAULT_VALUE => 123 ]
        );
        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(IntegerValueHolder::CLASS, $valueholder);
        $this->assertEquals(123, $valueholder->getValue());
    }

    public function testDefaultNullValue()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock()
        );
        $valueholder = $attribute->createValueHolder(false);
        $this->assertEquals(0, $valueholder->getValue());
    }

    public function testDefaultNullValueComparision()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock()
        );
        $valueholder = $attribute->createValueHolder(false);
        $this->assertTrue($valueholder->sameValueAs(0));
        $this->assertTrue($valueholder->sameValueAs('0'));
    }

    public function testNullInitializationWithNullOption()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_NULL_VALUE => -14 ]
        );
        $valueholder = $attribute->createValueHolder(false);
        $this->assertEquals(-14, $valueholder->getValue());
        $this->assertNotEquals(14, $valueholder->getValue());
    }

    public function testNullInitializationWithInvalidNullOption()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [
                IntegerAttribute::OPTION_NULL_VALUE => -14,
                IntegerAttribute::OPTION_MIN_VALUE => 14
            ]
        );
        $valueholder = $attribute->createValueHolder(false);
        $this->assertEquals(14, $valueholder->getValue());
        $this->assertNotEquals(-14, $valueholder->getValue());
    }

    public function testSetEmptyValue()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock()
        );
        $valueholder = $attribute->createValueHolder(false);
        $valueholder->setValue('');
        $this->assertEquals(0, $valueholder->getValue());
    }

    public function testSetEmptyValueWithNullOption()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_NULL_VALUE => -14 ]
        );
        $valueholder = $attribute->createValueHolder(false);
        $valueholder->setValue('');
        $this->assertEquals(-14, $valueholder->getValue());
        $this->assertNotEquals(14, $valueholder->getValue());
    }

    public function testSetEmptyValueWithMinOption()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_MIN_VALUE => -20 ]
        );
        $valueholder = $attribute->createValueHolder(false);
        $valueholder->setValue('');
        $this->assertNotEquals(0, $valueholder->getValue());
        $this->assertEquals(-20, $valueholder->getValue());
    }

    public function testSetEmptyValueWithNullAndMinOptions()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [
                IntegerAttribute::OPTION_NULL_VALUE => -15,
                IntegerAttribute::OPTION_MIN_VALUE => -20
            ]
        );
        $valueholder = $attribute->createValueHolder(false);
        $valueholder->setValue('');
        $this->assertEquals(-15, $valueholder->getValue());
        $this->assertNotEquals(15, $valueholder->getValue());
        $this->assertNotEquals(-20, $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_DEFAULT_VALUE => 1337 ]
        );
        $valueholder = $attribute->createValueHolder(true);

        $this->assertEquals(1337, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs('1337'));
        $this->assertFalse($valueholder->sameValueAs(1338));
    }

    public function testSettingBooleanTrueAsValueFails()
    {
        $attribute = new IntegerAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(true);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testOctalValues()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_ALLOW_OCTAL => true ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('010');
        $this->assertEquals(8, $valueholder->getValue());
    }

    public function testOctalValuesFails()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_ALLOW_OCTAL => false ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('010');
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testHexValues()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_ALLOW_HEX => true ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('0x10');
        $this->assertEquals(16, $valueholder->getValue());
    }

    public function testHexValuesFails()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ IntegerAttribute::OPTION_ALLOW_HEX => false ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('0x10');
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testMinMaxConstraint()
    {
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [
                IntegerAttribute::OPTION_MIN_VALUE => 3,
                IntegerAttribute::OPTION_MAX_VALUE => 5
            ]
        );

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue(1337);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new IntegerAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [
                IntegerAttribute::OPTION_MIN_VALUE => 1,
                IntegerAttribute::OPTION_MAX_VALUE => 5,
                IntegerAttribute::OPTION_DEFAULT_VALUE => 666
            ]
        );
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new IntegerAttribute(self::ATTR_NAME, $this->getTypeMock());
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return [
            [ null ],
            [ false ],
            [ true ],
            [ new stdClass() ]
        ];
    }
}
