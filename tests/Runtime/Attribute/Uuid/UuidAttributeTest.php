<?php

namespace Trellis\Tests\Runtime\Attribute\Uuid;

use Trellis\Common\Error\RuntimeException;
use Trellis\Runtime\Attribute\Uuid\UuidAttribute;
use Trellis\Runtime\Attribute\Uuid\UuidValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;

class UuidAttributeTest extends TestCase
{
    const ATTR_NAME = 'uuid';
    const REGEX_UUID_V4 = '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i';

    public function testCreate()
    {
        $uuid_attribute = new UuidAttribute(self::ATTR_NAME, $this->getTypeMock());
        $this->assertEquals($uuid_attribute->getName(), self::ATTR_NAME);
    }

    public function testDefaultValue()
    {
        $attribute = new UuidAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ UuidAttribute::OPTION_DEFAULT_VALUE => 'f615154d-1657-463c-ae11-240590c55360' ]
        );

        $this->assertEquals('f615154d-1657-463c-ae11-240590c55360', $attribute->getDefaultValue());
    }

    public function testInvalidValueSetting()
    {
        $attribute = new UuidAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();

        $result = $valueholder->setValue('asdf');
        $this->assertNotEquals('asdf', $valueholder->getValue());
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity());
    }

    public function testDefaultValueComparisonWorks()
    {
        $attribute = new UuidAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ UuidAttribute::OPTION_DEFAULT_VALUE => 'f615154d-1657-463c-ae11-240590c55360' ]
        );

        $valueholder = $attribute->createValueHolder(true);
        $this->assertEquals(1, preg_match(self::REGEX_UUID_V4, $valueholder->getValue()));
        $this->assertTrue($valueholder->isDefault());

        $result = $valueholder->setValue('asdf');
        $this->assertTrue($valueholder->isDefault());
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $uuid_attribute = new UuidAttribute(self::ATTR_NAME, $this->getTypeMock(), $options);

        $this->assertEquals($uuid_attribute->getName(), self::ATTR_NAME);
        $this->assertFalse($uuid_attribute->hasOption('snafu_flag'));

        foreach ($options as $optName => $optValue) {
            $this->assertTrue($uuid_attribute->hasOption($optName));
            $this->assertEquals($uuid_attribute->getOption($optName), $optValue);
        }
    }

    public function testCreateWithAutogenOption()
    {
        $options = [ UuidAttribute::OPTION_DEFAULT_VALUE => 'auto_gen' ];
        $uuid_attribute = new UuidAttribute(self::ATTR_NAME, $this->getTypeMock(), $options);

        $this->assertEquals($uuid_attribute->getName(), self::ATTR_NAME);
        $this->assertEquals('auto_gen', $uuid_attribute->getOption(UuidAttribute::OPTION_DEFAULT_VALUE));

        $valueholder = $uuid_attribute->createValueHolder(true);
        $this->assertEquals(1, preg_match(self::REGEX_UUID_V4, $valueholder->getValue()));
    }

    /**
     * @dataProvider getUuidAttributeFixture
     */
    public function testCreateValue($uuid)
    {
        $uuid_attribute = new UuidAttribute(self::ATTR_NAME, $this->getTypeMock());
        $value = $uuid_attribute->createValueHolder();
        $this->assertInstanceOf(UuidValueHolder::CLASS, $value);
        $value->setValue($uuid);
        $this->assertEquals($uuid, $value->getValue());
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getOptionsFixture()
    {
        // @todo generate random options.
        return [
            [
                [
                    'some_option_name' => 'some_option_value',
                    'another_option_name' => 'another_option_value'
                ],
                [
                    'some_option_name' => 23,
                    'another_option_name' => 5
                ],
                [
                    'some_option_name' => array('foo' => 'bar')
                ]
            ]
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getUuidAttributeFixture()
    {
        // @todo generate random (utf-8) text
        return [
            [ '9303ecdb-016f-4942-837a-a20c97b64310' ]
        ];
    }
}
