<?php

namespace Trellis\Tests\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Rule\Type\SanitizedFilenameRule;
use Trellis\Tests\TestCase;
use stdClass;

class SanitizedFilenameRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new SanitizedFilenameRule('text', []);
        $this->assertEquals('text', $rule->getName());
    }

    public function testEmptyStringIsOkayByDefault()
    {
        $rule = new SanitizedFilenameRule('text');
        $valid = $rule->apply('');
        $this->assertEquals('', $rule->getSanitizedValue());
    }

    public function testNewlinesStrippingSucceeds()
    {
        $rule = new SanitizedFilenameRule('text');
        $valid = $rule->apply("foo\t\r\nbar");
        $this->assertEquals("foobar", $rule->getSanitizedValue());
    }

    public function testNullByteRemoval()
    {
        $rule = new SanitizedFilenameRule('text');
        $valid = $rule->apply("some\x00file");
        $this->assertEquals("somefile", $rule->getSanitizedValue());
    }

    public function testStripRightToLeftOverride()
    {
        $rule = new SanitizedFilenameRule('text');
        $rtlo = "asdf\xE2\x80\xAEblah";
        $valid = $rule->apply($rtlo);
        $this->assertTrue($valid);
        $this->assertEquals('asdfblah', $rule->getSanitizedValue());
    }

    public function testStripLeftToRightOverride()
    {
        $rule = new SanitizedFilenameRule('text');
        $ltro = "foo\xE2\x80\xADbar";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals('foobar', $rule->getSanitizedValue());
    }

    public function testZeroWidthSpaceRemoval()
    {
        $rule = new SanitizedFilenameRule('text');
        $ltro = "some\xE2\x80\x8Btext";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals('sometext', $rule->getSanitizedValue());
    }

    public function testZeroWidthJoinerRemovalAtEndSucceeds()
    {
        $rule = new SanitizedFilenameRule('text');
        $ltro = "some\xE2\x80\x8Ctext\xE2\x80\x8D";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals("some\xE2\x80\x8Ctext", $rule->getSanitizedValue());
    }

    public function testZeroWidthNonJoinerRemovalAtEndSucceeds()
    {
        $rule = new SanitizedFilenameRule('text');
        $ltro = "some\xE2\x80\x8Dtext.jpg\xE2\x80\x8C";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals("some\xE2\x80\x8Dtext.jpg", $rule->getSanitizedValue());
    }

    public function testFilenameIsNoDotfileSucceeds()
    {
        $rule = new SanitizedFilenameRule('text');
        $ltro = ".hidden.jpg";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals("hidden.jpg", $rule->getSanitizedValue());
    }

    public function testDirectoryTraversalIsPrevented()
    {
        $rule = new SanitizedFilenameRule('text');
        $ltro = "./../../hidden.jpg";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals("hidden.jpg", $rule->getSanitizedValue());
    }

    public function testSpecialCharsAreReplaced()
    {
        $rule = new SanitizedFilenameRule('text');
        $ltro = "./../../hidden|file.jpg";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals("hidden-file.jpg", $rule->getSanitizedValue());
    }

    public function testSpecialCharsAreReplacedWithSpecificCharacter()
    {
        $rule = new SanitizedFilenameRule('text', [ SanitizedFilenameRule::OPTION_REPLACE_WITH => '_' ]);
        $ltro = "hidden|file/from%some}folder.jpg";
        $valid = $rule->apply($ltro);
        $this->assertTrue($valid);
        $this->assertEquals("hidden_file_from_some_folder.jpg", $rule->getSanitizedValue());
    }

    /**
     * @dataProvider provideValidSequences
     */
    public function testValidValue($valid_value, $assert_message = '')
    {
        $rule = new SanitizedFilenameRule('text', []);

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
        $rule = new SanitizedFilenameRule('text', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be invalid text');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should not be set as sanitized text');
    }

    /**
     * @dataProvider provideIllformedSequences
     */
    public function testStrippingOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new SanitizedFilenameRule('text');

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
