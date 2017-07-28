<?php

namespace Trellis\Tests\Runtime\Attribute\HtmlLinkList;

use Trellis\Runtime\Attribute\HtmlLink\HtmlLinkRule;
use Trellis\Runtime\Attribute\HtmlLinkList\HtmlLinkListRule;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLink;
use Trellis\Tests\TestCase;
use stdClass;

class HtmlLinkListRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new HtmlLinkListRule('linklist', []);
        $this->assertEquals('linklist', $rule->getName());
    }

    public function testEmptyListIsValid()
    {
        $rule = new HtmlLinkListRule('linklist', []);
        $valid = $rule->apply([]);
        $this->assertTrue($valid);
    }

    public function testCompleteHtmlLinkDataIsValid()
    {
        $rule = new HtmlLinkListRule('linklist', []);
        $valid = $rule->apply([
            [
                HtmlLink::PROPERTY_HREF => 'http://www.example.com/foo/bar.jpg',
                HtmlLink::PROPERTY_TEXT => 'some text',
                HtmlLink::PROPERTY_TITLE => 'some title',
                HtmlLink::PROPERTY_TARGET => '_blank',
                HtmlLink::PROPERTY_HREFLANG => 'de',
                HtmlLink::PROPERTY_REL => 'nofollow',
                HtmlLink::PROPERTY_DOWNLOAD => true,
            ]
        ]);
        $this->assertTrue($valid);
    }


    public function testMinimumHtmlLinkListDataIsValid()
    {
        $rule = new HtmlLinkListRule('linklist', []);
        $valid = $rule->apply([
            [ HtmlLink::PROPERTY_HREF => 'http://foo/bar.jpg' ]
        ]);
        $this->assertTrue($valid);
    }

    public function testMinimumHtmlLinkListIsValid()
    {
        $rule = new HtmlLinkListRule('linklist', []);
        $valid = $rule->apply([
            HtmlLink::createFromArray([HtmlLink::PROPERTY_HREF => 'https://example.com/asdf.jpg'])
        ]);
        $this->assertTrue($valid);
    }

    public function testNullByteRemoval()
    {
        $link_data = [
            [
                HtmlLink::PROPERTY_HREF => "https://example.com/some\x00file",
                HtmlLink::PROPERTY_TEXT => "some\x00text",
            ]
        ];

        $rule = new HtmlLinkListRule('linklist', []);

        $valid = $rule->apply($link_data);

        $this->assertTrue($valid);

        $this->assertTrue(is_array($rule->getSanitizedValue()));

        $link = $rule->getSanitizedValue()[0];

        $this->assertEquals("https://example.com/somefile", $link->getHref());
        $this->assertEquals("sometext", $link->getText());
    }

    /**
     * @dataProvider provideValidValues
     */
    public function testAcceptanceOfValidValues($valid_value, $assert_message = '')
    {
        $rule = new HtmlLinkListRule('linklist', []);
        $this->assertTrue($rule->apply($valid_value), $assert_message . ' should be accepted');
        $this->assertNotNull($rule->getSanitizedValue(), $assert_message . ' should not be null for a valid value');
    }

    public function provideValidValues()
    {
        return [
            [
                [
                    [
                        HtmlLink::PROPERTY_HREF => 'http://some/file.jpg',
                    ]
                ],
                'only 1 link w/ only href'
            ],
            [
                [
                    [
                        HtmlLink::PROPERTY_TEXT => 'some text',
                        HtmlLink::PROPERTY_HREF => 'http://example.com/some/path?q=foo#fragment',
                    ]
                ],
                '1 link w/ href and text'
            ],
            [
                [
                    [
                        HtmlLink::PROPERTY_HREF => 'http://example.com/some/path?q=foo#fragment',
                        HtmlLink::PROPERTY_TEXT => 'some text',
                        HtmlLink::PROPERTY_TITLE => 'some title',
                    ],
                    [
                        HtmlLink::PROPERTY_HREF => 'http://example.com/some/path?q=foo#fragment',
                        HtmlLink::PROPERTY_TEXT => 'some text',
                    ]
                ],
                '2 links w/ hrefs and text and title'
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testRejectionOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new HtmlLinkListRule('scalar', []);
        $this->assertFalse($rule->apply($invalid_value), $assert_message . ' should be rejected');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should be null for an invalid value');
    }

    public function provideInvalidValues()
    {
        return [
            [ new stdClass(), 'stdClass object' ],
            [ ['foo'], 'simple array' ],
            [ null, 'NULL' ],
            [ '', 'empty string' ],
            [ 'some string', 'simple string' ],
            [ 123, 'integer value' ],
            [ 123.456, 'float value' ],
            [ true, 'boolean TRUE' ],
            [ false, 'boolean FALSE' ],
            [ 1e12, 'float value in e-notation' ],
            [ -123, 'negative integer value' ],
            [ -345.123, 'negative float value' ],
            [
                [
                    [
                        HtmlLink::PROPERTY_HREF => 'http://...example.com/some/path?q=foo#fragment'
                    ]
                ],
                'link w/ broken href url'
            ],
        ];
    }
}
