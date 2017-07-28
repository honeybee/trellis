<?php

namespace Trellis\Tests\Runtime\Attribute\HtmlLinkList;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\HtmlLinkList\HtmlLinkListAttribute;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLink;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLinkRule;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use stdClass;

class HtmlLinkListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new HtmlLinkListAttribute('linklist', $this->getTypeMock());
        $this->assertEquals($attribute->getName(), 'linklist');
        $this->assertEquals([], $attribute->getNullValue());
        $this->assertEquals([], $attribute->getDefaultValue());
    }

    public function testValueComparison()
    {
        $link_data = [
            HtmlLink::PROPERTY_HREF => 'http://example.com/some.jpg',
            HtmlLink::PROPERTY_TEXT => 'some text',
        ];

        $link2_data = $link_data;
        $link2_data[HtmlLink::PROPERTY_TITLE] = 'some title';

        $link_list_data = [
            $link_data,
            $link2_data,
        ];

        $expected_list = [
            HtmlLink::createFromArray($link_data),
            HtmlLink::createFromArray($link2_data),
        ];

        $link3_data = $link2_data;
        $link3_data[HtmlLink::PROPERTY_TITLE] = 'some other title';

        $expected_other_list = [
            HtmlLink::createFromArray($link_data),
            HtmlLink::createFromArray($link3_data),
        ];

        $attribute = new HtmlLinkListAttribute('linklist', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($link_list_data);

        $this->assertInstanceOf(HtmlLink::CLASS, $valueholder->getValue()[0]);
        $this->assertInstanceOf(HtmlLink::CLASS, $valueholder->getValue()[1]);

        $this->assertTrue($valueholder->sameValueAs($expected_list));
        $this->assertFalse($valueholder->sameValueAs($expected_other_list));
    }

    public function testToNativeRoundtrip()
    {
        $link_list_data = [
            [
                HtmlLink::PROPERTY_HREF => 'http://example.com/some.jpg',
                HtmlLink::PROPERTY_TEXT => 'some text',
            ]
        ];

        $native = [
            [
                HtmlLink::PROPERTY_HREF => 'http://example.com/some.jpg',
                HtmlLink::PROPERTY_TEXT => 'some text',
                HtmlLink::PROPERTY_TITLE => '',
                HtmlLink::PROPERTY_HREFLANG => '',
                HtmlLink::PROPERTY_REL => '',
                HtmlLink::PROPERTY_TARGET => '',
                HtmlLink::PROPERTY_DOWNLOAD => false,
            ]
        ];

        $attribute = new HtmlLinkListAttribute('linklist', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($link_list_data);

        $links = $valueholder->getValue();

        $this->assertTrue(is_array($links));
        $this->assertInstanceOf(HtmlLink::CLASS, $links[0]);
        $this->assertEquals($native, $valueholder->toNative());

        $result = $valueholder->setValue($valueholder->toNative());

        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity());
        $this->assertInstanceOf(HtmlLink::CLASS, $valueholder->getValue()[0]);
        $this->assertEquals('http://example.com/some.jpg', $valueholder->getValue()[0]->getHref());
        $this->assertEquals($native, $valueholder->toNative());
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);
        $attribute = new HtmlLinkListAttribute(
            'linkinvaliddefaultvalue',
            $this->getTypeMock(),
            [ HtmlLinkListAttribute::OPTION_DEFAULT_VALUE => 'trololo' ]
        );
        $attribute->getDefaultValue();
    }

    public function testMinMaxStringLengthConstraint()
    {
        $data = [
            HtmlLink::PROPERTY_HREF => 'http://heise.de',
            HtmlLink::PROPERTY_TEXT => 'some too long text',
        ];

        $attribute = new HtmlLinkListAttribute(
            'htmllinklistminmaxstringlength',
            $this->getTypeMock(),
            [
                HtmlLinkListAttribute::OPTION_TEXT_MIN_LENGTH => 3,
                HtmlLinkListAttribute::OPTION_TEXT_MAX_LENGTH => 5
            ]
        );

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testMaxCountConstraint()
    {
        $data = [
            [
                HtmlLink::PROPERTY_HREF => 'http://heise.de',
                HtmlLink::PROPERTY_TEXT => 'some text',
            ],
            [
                HtmlLink::PROPERTY_HREF => 'http://example.com',
                HtmlLink::PROPERTY_TITLE => 'some title',
            ],
        ];

        $attribute = new HtmlLinkListAttribute(
            'htmllinklistmaxcount',
            $this->getTypeMock(),
            [ HtmlLinkListAttribute::OPTION_MAX_COUNT => 1 ]
        );

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getDefaultValue(), $attribute->getNullValue());
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        $data = [
            [
                HtmlLink::PROPERTY_HREF => 'http://heise.de',
                HtmlLink::PROPERTY_TEXT => 'some text',
            ],
        ];
        $validation_result = $valueholder->setValue($data);
        $this->assertFalse($valueholder->isDefault());
        $this->assertFalse($valueholder->isNull());
        $this->assertTrue($validation_result->getSeverity() === IncidentInterface::SUCCESS);
        $expected = [
                HtmlLink::PROPERTY_HREF => 'http://heise.de',
                HtmlLink::PROPERTY_TEXT => 'some text',
                HtmlLink::PROPERTY_TITLE => '',
                HtmlLink::PROPERTY_TARGET => '',
                HtmlLink::PROPERTY_HREFLANG => '',
                HtmlLink::PROPERTY_REL => '',
                HtmlLink::PROPERTY_DOWNLOAD => false,
        ];
        $this->assertEquals($expected, $valueholder->getValue()[0]->toNative());
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new HtmlLinkListAttribute('linklistwithInvalidValue', $this->getTypeMock());
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
            [ false ],
            [ true ],
            [ new stdClass() ],
            [
                [
                    [
                        HtmlLink::PROPERTY_HREF => 'localhost'
                    ]
                ]
            ],
            [
                [
                    [
                        HtmlLink::PROPERTY_HREF => 'http://..com'
                    ]
                ]
            ],
            [
                [
                    [
                        HtmlLink::PROPERTY_HREF => 'javascript:alert(1)'
                    ]
                ]
            ],
        ];
    }
}
