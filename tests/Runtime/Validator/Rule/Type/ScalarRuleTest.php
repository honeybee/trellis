<?php

namespace Trellis\Tests\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Rule\Type\ScalarRule;
use Trellis\Tests\TestCase;
use stdClass;

class ScalarRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new ScalarRule('text', []);
        $this->assertEquals('text', $rule->getName());
    }

    public function testNullByteRemoval()
    {
        $rule = new ScalarRule('text', [ ]);
        $valid = $rule->apply("some\x00file");
        $this->assertEquals("somefile", $rule->getSanitizedValue());
    }

    public function testDefaultRemoveControlChars()
    {
        $rule = new ScalarRule('scalartext', [ ]);
        $valid = $rule->apply("some\t\nfile");
        $this->assertEquals("some\tfile", $rule->getSanitizedValue());
    }

    public function testRemoveControlCharsExceptTabAndNewlines()
    {
        $rule = new ScalarRule('scalartext', [
            ScalarRule::OPTION_ALLOW_CRLF => true,
            ScalarRule::OPTION_ALLOW_TAB => true
        ]);
        $valid = $rule->apply("some\t\r\nfile");
        $this->assertEquals("some\t\r\nfile", $rule->getSanitizedValue());
    }

    /**
     * @dataProvider provideValidValues
     */
    public function testAcceptanceOfValidValues($valid_value, $assert_message = '')
    {
        $rule = new ScalarRule('scalar', []);
        $this->assertTrue($rule->apply($valid_value), $assert_message . ' should be accepted');
        $this->assertNotNull($rule->getSanitizedValue(), $assert_message . ' should not be null for a valid value');
    }

    public function provideValidValues()
    {
        return array(
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

    /**
     * @dataProvider provideInvalidValues
     */
    public function testRejectionOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new ScalarRule('scalar', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be rejected');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should be null for an invalid value');
    }

    public function provideInvalidValues()
    {
        return array(
            array(new stdClass(), 'stdClass object'),
            array([], 'empty array'),
            array(['foo'], 'simple array'),
        );
    }
}
