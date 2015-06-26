<?php

namespace Trellis\Tests\Runtime\Attribute\Textarea;

use Trellis\Runtime\Attribute\Textarea\TextareaAttribute;
use Trellis\Runtime\Attribute\Textarea\TextareaValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;

class TextareaAttributeTest extends TestCase
{
    const ATTR_NAME = 'textarea_attribute';

    public function testCreate()
    {
        $text_attribute = new TextareaAttribute(self::ATTR_NAME, $this->getTypeMock());
        $this->assertEquals($text_attribute->getName(), self::ATTR_NAME);
    }

    public function testDefaultTabAndNewlineNormalizationBehaviour()
    {
        $string = "\n CHA\x00RSET - WÄHLE \t\t\r\nUTF-8 AS SENSIBLE DEFAULT!  ";
        $string_trimmed = "CHARSET - WÄHLE \t\t\r\nUTF-8 AS SENSIBLE DEFAULT!";
        $attribute = new TextareaAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue($string);
        $this->assertTrue($string_trimmed === $valueholder->getValue());
    }
}
