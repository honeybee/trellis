<?php

namespace Trellis\Tests\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Rule\Type\TextRule;
use Trellis\Tests\TestCase;
use stdClass;

class TextRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new TextRule('text', []);
        $this->assertEquals('text', $rule->getName());
    }

    public function testEmptyStringIsOkayByDefault()
    {
        $rule = new TextRule('text', []);
        $valid = $rule->apply('');
        $this->assertEquals('', $rule->getSanitizedValue());
    }

    public function testByDefaultNewlinesAreNotNormalized()
    {
        $rule = new TextRule('text', [
            //'trim' => false,
            //'strip_control_characters' => false,
            TextRule::OPTION_ALLOW_CRLF => true
        ]);
        $valid = $rule->apply("foo\t\r\nbar");
        $this->assertEquals("foo\t\r\nbar", $rule->getSanitizedValue());
    }

    public function testUtf8TrimWorks()
    {
        $s = html_entity_decode(" Hello &#160; ");
        $rule = new TextRule('text', []);
        $valid = $rule->apply($s);
        $this->assertSame("Hello", $rule->getSanitizedValue());
    }

/*    public function testTrimAfterInvalidUtf8StrippingWorks()
    {
        $s = "  \xc3\x28a\n \t\n \r\n   "; // invalid 2 octet sequence with some whitespace around
        $rule = new TextRule('text', [
            TextRule::OPTION_REJECT_INVALID_UTF8 => false,
            TextRule::OPTION_STRIP_INVALID_UTF8 => true,
            TextRule::OPTION_TRIM => true,
        ]);
        $valid = $rule->apply($s);
        $this->assertTrue($valid);
        $this->assertSame("a", $rule->getSanitizedValue());
    }
*/

    public function testNewlinesCanBeNormalized()
    {
        $rule = new TextRule('text', [
            //'trim' => false,
            //'strip_control_characters' => false,
            TextRule::OPTION_ALLOW_CRLF => true,
            TextRule::OPTION_NORMALIZE_NEWLINES => true
        ]);
        $valid = $rule->apply("foo\t\r\nbar");
        $this->assertEquals("foo\t\nbar", $rule->getSanitizedValue());
    }

    public function testNewlineNormalizationDisabled()
    {
        $rule = new TextRule('text', [
            TextRule::OPTION_TRIM => false,
            TextRule::OPTION_STRIP_CONTROL_CHARACTERS => false,
            TextRule::OPTION_NORMALIZE_NEWLINES => false
        ]);
        $valid = $rule->apply("\r\n");
        $this->assertEquals("\r\n", $rule->getSanitizedValue());
    }

    public function testNullByteRemoval()
    {
        $rule = new TextRule('text', [ ]);
        $valid = $rule->apply("some\x00file");
        $this->assertEquals("somefile", $rule->getSanitizedValue());
    }

    public function testStripRightToLeftOverride()
    {
        $rule = new TextRule('text', [ TextRule::OPTION_STRIP_DIRECTION_OVERRIDES => true ]);
        $rtlo = "asdf\xE2\x80\xAEblah";
        $valid = $rule->apply($rtlo);
        $this->assertTrue($valid);
        $this->assertEquals('asdfblah', $rule->getSanitizedValue());
    }

    public function testStripLeftToRightOverride()
    {
        $rule = new TextRule('text', [ TextRule::OPTION_STRIP_DIRECTION_OVERRIDES => true ]);
        $ltro = "foo\xE2\x80\xADbar";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals('foobar', $rule->getSanitizedValue());
    }

    public function testZeroWidthSpaceRemoval()
    {
        $rule = new TextRule('text', [ TextRule::OPTION_STRIP_ZERO_WIDTH_SPACE => true ]);
        $ltro = "some\xE2\x80\x8Btext";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals('sometext', $rule->getSanitizedValue());
    }

    public function testDefaultRemoveControlChars()
    {
        $rule = new TextRule('text', [ ]);
        $valid = $rule->apply("so.me\t\nfile");
        $this->assertEquals("so.me\tfile", $rule->getSanitizedValue());
    }

    public function testRemoveControlCharsExceptTabAndNewlines()
    {
        $rule = new TextRule('text', [
            //'strip_control_characters' => true,
            TextRule::OPTION_ALLOW_CRLF => true,
            TextRule::OPTION_ALLOW_TAB => true
        ]);
        $valid = $rule->apply("some\t\r\nfile");
        $this->assertEquals("some\t\r\nfile", $rule->getSanitizedValue());
    }

    /**
     * @dataProvider provideValidSequences
     */
    public function testValidValue($valid_value, $assert_message = '')
    {
        $rule = new TextRule('text', []);

        $valid = $rule->apply($valid_value);
        $this->assertTrue($valid, $assert_message . ' should be valid text');
        $this->assertTrue(
            $rule->getSanitizedValue() === $valid_value,
            $assert_message . ' should be set as sanitized text'
        );
    }

    public function provideValidSequences()
    {
        return array(
            array("ascii", 'simple ASCII'),
            array("κόσμε", 'greek word "kosme"'),
            array("\xc3\xb1", 'valid 2 octet sequence'),
            array("\xe2\x82\xa1", 'valid 3 octet sequence'),
            array("\xf0\x90\x8c\xbc", 'valid 4 octet sequence'),
        );
    }

    /**
     * @dataProvider provideInvalidSequences
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $rule = new TextRule('text', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be invalid text');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should not be set as sanitized text');
    }

    /**
     * @dataProvider provideIllformedSequences
     */
    public function testIllformedValue($invalid_value, $assert_message = '')
    {
        $rule = new TextRule('text', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be invalid text');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should not be set as sanitized text');
    }

    /**
     * @dataProvider provideIllformedSequences
     */
    public function testAcceptingInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new TextRule('text', [
            TextRule::OPTION_REJECT_INVALID_UTF8 => false,
            TextRule::OPTION_STRIP_INVALID_UTF8 => false,
            TextRule::OPTION_TRIM => true,
            TextRule::OPTION_STRIP_NULL_BYTES => true,
            TextRule::OPTION_STRIP_CONTROL_CHARACTERS => true,
            TextRule::OPTION_NORMALIZE_NEWLINES => true,
            TextRule::OPTION_ALLOW_CRLF => false,
            TextRule::OPTION_ALLOW_TAB => false
        ]);

        $this->assertTrue(
            $rule->apply($invalid_value),
            $assert_message . ' should be accepted'
        );

        $this->assertEquals(
            $rule->getSanitizedValue(),
            $invalid_value,
            $assert_message . ' should be set as sanitized text w/o stripped characters'
        );
    }

    /**
     * @dataProvider provideIllformedSequences
     */
    public function testStrippingOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new TextRule('text', [
            TextRule::OPTION_REJECT_INVALID_UTF8 => false,
            TextRule::OPTION_STRIP_INVALID_UTF8 => true
        ]);

        $this->assertTrue(
            $rule->apply($invalid_value),
            $assert_message . ' should be accepted with certain characters stripped'
        );

        $this->assertNotNull(
            $rule->getSanitizedValue(),
            $assert_message . ' should be set as sanitized text w/ some chars stripped'
        );
    }

    public function provideIllformedSequences()
    {
        return array(
            array("\xfe", 'impossible byte FE'),
            array("\xff", 'impossible byte FF'),
            array("\xfe\xfe\xff\xff", 'impossible byte FEFEFFFF'),
            array("\xc3\x28", 'invalid 2 octet sequence'),
            array("\xa0\xa1", 'invalid sequence identifier'),
            array("\xe2\x28\xa1", 'invalid 3 octet sequence (in 2nd octet)'),
            array("\xe2\x82\x28", 'invalid 3 octet sequence (in 3rd octet)'),
            array("\xf0\x28\x8c\xbc", 'invalid 4 octet sequence (in 2nd octet)'),
            array("\xf0\x90\x28\xbc", 'invalid 4 octet sequence (in 3rd octet)'),
            array("\xf0\x28\x8c\x28", 'invalid 4 octet sequence (in 4th octet)'),
            array("\xf8\xa1\xa1\xa1\xa1", 'invalid 5 octet sequence'),
            array("\xfc\xa1\xa1\xa1\xa1\xa1", 'invalid 6 octet sequence'),
            array("\xC0\xAF", 'slash character 0x2F as overlong sequence 0xC00xAF'),
        );
    }

    public function provideInvalidSequences()
    {
        return array(
            array(null, 'NULL'),
            array(false, 'FALSE'),
            array(true, 'TRUE'),
            array(new stdClass(), 'stdClass object'),
        );
    }
}
