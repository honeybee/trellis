<?php

namespace Trellis\Tests\Runtime\Attribute\HtmlLink;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLink;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLinkAttribute;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use stdClass;

class HtmlLinkAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new HtmlLinkAttribute('htmllink', $this->getTypeMock());
        $this->assertEquals($attribute->getName(), 'htmllink');
        $this->assertEquals(null, $attribute->getNullValue());
        $this->assertEquals(null, $attribute->getDefaultValue());
    }

    public function testValueComparison()
    {
        $link_data = [
            HtmlLink::PROPERTY_HREF => 'https://foo.bar/some.jpg',
            HtmlLink::PROPERTY_TEXT => 'some awesome string',
        ];

        $link2_data = $link_data;
        $link2_data[HtmlLink::PROPERTY_TITLE] = 'some title';

        $attribute = new HtmlLinkAttribute('htmllink', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($link_data);

        $this->assertInstanceOf(HtmlLink::CLASS, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($link_data));
        $this->assertFalse($valueholder->sameValueAs($link2_data));
    }

    public function testToNativeRoundtrip()
    {
        $link_data = [
            HtmlLink::PROPERTY_HREF => 'https://example.com/some.jpg',
            HtmlLink::PROPERTY_TEXT => 'some text',
            HtmlLink::PROPERTY_TITLE => 'some title',
            HtmlLink::PROPERTY_DOWNLOAD => true,
        ];

        $native = [
            HtmlLink::PROPERTY_HREF => 'https://example.com/some.jpg',
            HtmlLink::PROPERTY_TEXT => 'some text',
            HtmlLink::PROPERTY_TITLE => 'some title',
            HtmlLink::PROPERTY_HREFLANG => '',
            HtmlLink::PROPERTY_REL => '',
            HtmlLink::PROPERTY_TARGET => '',
            HtmlLink::PROPERTY_DOWNLOAD => true,
        ];

        $attribute = new HtmlLinkAttribute('htmllink', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue(HtmlLink::createFromArray($link_data));
        $this->assertInstanceOf(HtmlLink::CLASS, $valueholder->getValue());

        $this->assertEquals($native, $valueholder->toNative());
        $result = $valueholder->setValue($valueholder->toNative());
        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertInstanceOf(HtmlLink::CLASS, $valueholder->getValue());
        $this->assertEquals($native, $valueholder->toNative());
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new HtmlLinkAttribute(
            'linkinvaliddefaultvalue',
            $this->getTypeMock(),
            [ HtmlLinkAttribute::OPTION_DEFAULT_VALUE => 5.00000001 ]
        );
        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new HtmlLinkAttribute('linkInvalidValue', $this->getTypeMock());
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
            [ false ],
            [ true ],
            [ new stdClass() ],
            [
                [
                    HtmlLink::PROPERTY_HREF => 'sad.jpg',
                ]
            ],
            [
                [
                    HtmlLink::PROPERTY_TEXT => 'sad',
                ]
            ],
        ];
    }
}
