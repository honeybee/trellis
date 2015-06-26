<?php

namespace Trellis\Tests\Runtime\Attribute\Number;

use Trellis\Runtime\Attribute\Number\NumberAttribute;
use Trellis\Runtime\Attribute\Number\NumberValueHolder;
use Trellis\Tests\TestCase;

class NumberAttributeTest extends TestCase
{
    const ATTR_NAME = 'test_int_attribute';

    public function testCreateAttribute()
    {
        $number_attribute = new NumberAttribute(self::ATTR_NAME, $this->getTypeMock());
        $this->assertEquals($number_attribute->getName(), self::ATTR_NAME);
        $this->assertInstanceOf(NumberAttribute::CLASS, $number_attribute);
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateAttributeWithOptions(array $options)
    {
        $number_attribute = new NumberAttribute(self::ATTR_NAME, $this->getTypeMock(), $options);

        $this->assertEquals($number_attribute->getName(), self::ATTR_NAME);
        $this->assertFalse($number_attribute->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($number_attribute->hasOption($optName));
            $this->assertEquals($number_attribute->getOption($optName), $optValue);
        }
    }

    /**
     * @dataProvider getIntegerFixture
     */
    public function testCreateValue($intValue)
    {
        $number_attribute = new NumberAttribute(self::ATTR_NAME, $this->getTypeMock());
        $value = $number_attribute->createValueHolder();
        $this->assertInstanceOf(NumberValueHolder::CLASS, $value);
        $value->setValue($intValue);
        $this->assertEquals($intValue, $value->getValue());
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
                    'some_option_name' => [ 'foo' => 'bar' ]
                ]
            ]
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getIntegerFixture()
    {
        // @todo generate random (utf-8) text
        $fixtures = [];
        $fixtures[] = [ 2 ];
        $fixtures[] = [ 23 ];
        $fixtures[] = [ 2035 ];

        return $fixtures;
    }
}
