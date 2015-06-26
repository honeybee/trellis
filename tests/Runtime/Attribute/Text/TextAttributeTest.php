<?php

namespace Trellis\Tests\Runtime\Attribute\Text;

use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Attribute\Text\TextValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;

class TextAttributeTest extends TestCase
{
    const ATTR_NAME = 'test_text_attribute';

    public function testCreate()
    {
        $text_attribute = new TextAttribute(self::ATTR_NAME, $this->getTypeMock());
        $this->assertEquals($text_attribute->getName(), self::ATTR_NAME);
    }

    public function testUtf8Handling()
    {
        $string = 'CHARSET - WÄHLE UTF-8 AS SENSIBLE DEFAULT!  ';
        $string_trimmed = 'CHARSET - WÄHLE UTF-8 AS SENSIBLE DEFAULT!';
        $text_attribute = new TextAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $text_attribute->createValueHolder();
        $result = $valueholder->setValue($string);
        $this->assertTrue($string_trimmed === $valueholder->getValue(), 'utf8 string should be trimmed');
    }

    /**
     * @dataProvider getOptionsFixture
     */
    public function testCreateWithOptions(array $options)
    {
        $text_attribute = new TextAttribute(self::ATTR_NAME, $this->getTypeMock(), $options);

        $this->assertEquals($text_attribute->getName(), self::ATTR_NAME);
        $this->assertFalse($text_attribute->hasOption('snafu_flag'));
        foreach ($options as $optName => $optValue) {
            $this->assertTrue($text_attribute->hasOption($optName));
            $this->assertEquals($text_attribute->getOption($optName), $optValue);
        }
    }

    public function testCreateValue()
    {
        $text_attribute = new TextAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $text_attribute->createValueHolder();
        $this->assertInstanceOf(TextValueHolder::CLASS, $valueholder);
        $valueholder->setValue('omgomgomg');
        $this->assertEquals('omgomgomg', $valueholder->getValue());
    }

    public function testAcceptZeroWidthSpace()
    {
        $text_attribute = new TextAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $text_attribute->createValueHolder();
        $zero_width_space = "some\xE2\x80\x8Btext";
        $result = $valueholder->setValue($zero_width_space);
        $this->assertTrue($result->getSeverity() === IncidentInterface::SUCCESS);
        $this->assertEquals($zero_width_space, $valueholder->getValue());
    }
/*
    public function testSpoofcheckIncomingRejectsZeroWidthSpace()
    {
        $text_attribute = new TextAttribute(self::ATTR_NAME, [ 'spoofcheck_incoming' => true ]);
        $valueholder = $text_attribute->createValueHolder();
        $zero_width_space = "some\xE2\x80\x8Btext";
        $result = $valueholder->setValue($zero_width_space);
        $this->assertFalse($result->getSeverity() === IncidentInterface::SUCCESS);
        $this->assertEquals('', $valueholder->getValue());
    }

    public function testSpoofcheckResultingValueRejectsZeroWidthSpace()
    {
        $text_attribute = new TextAttribute(self::ATTR_NAME, [ 'spoofcheck_result' => true ]);
        $valueholder = $text_attribute->createValueHolder();
        $zero_width_space = "some\xE2\x80\x8Btext";
        $result = $valueholder->setValue($zero_width_space);
        $this->assertFalse($result->getSeverity() === IncidentInterface::SUCCESS);
        $this->assertEquals('', $valueholder->getValue());
    }
 */
/*
    public function testSpoofcheckResultingValueSucceedsAsZeroWidthSpaceIsTrimmed()
    {
        $text_attribute = new TextAttribute(self::ATTR_NAME, [
            'strip_zero_width_space' => true,
            // 'spoofcheck_incoming' => true
            'spoofcheck_result' => true
        ]);
        $valueholder = $text_attribute->createValueHolder();
        $zero_width_space = "some\xE2\x80\x8Btext";
        $result = $valueholder->setValue($zero_width_space);
        $this->assertTrue($result->getSeverity() === IncidentInterface::SUCCESS);
        $this->assertEquals('sometext', $valueholder->getValue());
    }
*/
    public function testValidationSuccess()
    {
        $text_attribute = new TextAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ TextAttribute::OPTION_MIN_LENGTH => 3, TextAttribute::OPTION_MAX_LENGTH => 10 ]
        );

        $result = $text_attribute->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IncidentInterface::SUCCESS);
    }

    public function testValidationError()
    {
        $text_attribute = new TextAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ TextAttribute::OPTION_MIN_LENGTH => 3, TextAttribute::OPTION_MAX_LENGTH => 5 ]
        );

        $result = $text_attribute->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IncidentInterface::ERROR);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getOptionsFixture()
    {
        // @todo generate random options.
        $fixtures = [];
        $fixtures[] = [
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
        ];

        return $fixtures;
    }
}
