<?php

namespace Trellis\Tests\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Rule\Type\KeyValueListRule;
use Trellis\Tests\TestCase;
use stdClass;

class KeyValueListRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new KeyValueListRule('text', []);
        $this->assertEquals('text', $rule->getName());
    }

    public function testNullByteRemoval()
    {
        $rule = new KeyValueListRule('text', []);
        $valid = $rule->apply(['nullbyte' => "some\x00file"]);
        $this->assertEquals(['nullbyte' => "somefile"], $rule->getSanitizedValue());
    }

    public function testDefaultRemoveControlChars()
    {
        $rule = new KeyValueListRule('scalartext', []);
        $valid = $rule->apply(['foo' => "some\t\nfile"]);
        $this->assertEquals(['foo' => "some\tfile"], $rule->getSanitizedValue());
    }

    public function testRemoveControlCharsExceptTabAndNewlines()
    {
        $rule = new KeyValueListRule('scalartext', [
            KeyValueListRule::OPTION_ALLOW_CRLF => true,
            KeyValueListRule::OPTION_ALLOW_TAB => true
        ]);
        $valid = $rule->apply(['text' => "some\t\r\nfile"]);
        $this->assertEquals(['text' => "some\t\r\nfile"], $rule->getSanitizedValue());
    }

    public function testMultipleOptionsUsage()
    {
        $rule = new KeyValueListRule('asdf', [
            KeyValueListRule::OPTION_ALLOW_CRLF => true,
            KeyValueListRule::OPTION_ALLOW_TAB => true,
            KeyValueListRule::OPTION_ALLOW_INFINITY => true,
            KeyValueListRule::OPTION_ALLOW_OCTAL => true,
        ]);

        $valid = $rule->apply(
            [
                'text' => "  some\t\r\nfile  ",
                'float-negative-infinity' => log(0),
                'integer-as-octal' => 010,
                'bool' => false
            ]
        );

        $this->assertEquals(
            [
                'text' => "some\t\r\nfile",
                'float-negative-infinity' => '-INF',
                'integer-as-octal' => 8,
                'bool' => false

            ],
            $rule->getSanitizedValue()
        );
    }

    /**
     * @dataProvider provideValidValues
     */
    public function testAcceptanceOfValidValues($valid_value, $assert_message = '')
    {
        $rule = new KeyValueListRule('scalar', []);
        $this->assertTrue($rule->apply($valid_value), $assert_message . ' should be accepted');
        $this->assertNotNull($rule->getSanitizedValue(), $assert_message . ' should not be null for a valid value');
    }

    public function provideValidValues()
    {
        return array(
            array([], 'empty array'),
            array(['foo' => 'bar'], 'simple assoc array'),
            array(['foo' => null], 'null value'),
            array(
                [
                    'foo' => 'bar',
                    'int' => 1337,
                    'float' => 1337.987,
                    'bool' => true
                ],
                'simple assoc array with different scalar values'
            ),
        );
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testRejectionOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new KeyValueListRule('scalar', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be rejected');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should be null for an invalid value');
    }

    public function provideInvalidValues()
    {
        return array(
            array(new stdClass(), 'stdClass object'),
            array(['foo'], 'simple array'),
            array(['foo' => ['bar']], 'nested assoc array'),
            array(null, 'NULL'),
            array('', 'empty string'),
            array('some string', 'simple string'),
            array(123, 'integer value'),
            array(123.456, 'float value'),
            array(true, 'boolean TRUE'),
            array(false, 'boolean FALSE'),
            array(1e12, 'float value in e-notation'),
            array(-123, 'negative integer value'),
            array(-345.123, 'negative float value'),
        );
    }
}
