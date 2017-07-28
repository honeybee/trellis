<?php

namespace Trellis\Tests\Runtime\Attribute\HtmlLink;

use Trellis\Runtime\Attribute\HtmlLink\HtmlLinkRule;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLink;
use Trellis\Tests\TestCase;
use stdClass;

class HtmlLinkRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new HtmlLinkRule('link', []);
        $this->assertEquals('link', $rule->getName());
    }

    public function testEmptyHtmlLinkDataIsInvalid()
    {
        $rule = new HtmlLinkRule('link', []);
        $valid = $rule->apply([]);
        $this->assertTrue($valid);
    }

    public function testCompleteHtmlLinkDataIsValid()
    {
        $rule = new HtmlLinkRule('link', []);
        $valid = $rule->apply(
            [
                HtmlLink::PROPERTY_HREF => 'https://example.com/foo/bar.jpg',
                HtmlLink::PROPERTY_TITLE => 'some title',
                HtmlLink::PROPERTY_TEXT => 'some caption',
                HtmlLink::PROPERTY_TARGET => '_blank',
                HtmlLink::PROPERTY_HREFLANG => 'en-gb',
                HtmlLink::PROPERTY_DOWNLOAD => true,
                HtmlLink::PROPERTY_REL => 'nofollow',
            ]
        );
        $this->assertTrue($valid);
    }
    public function testMinimumHtmlLinkDataIsValid()
    {
        $rule = new HtmlLinkRule('link', []);
        $valid = $rule->apply([HtmlLink::PROPERTY_HREF => 'http://exmaple.com/foo/bar']);
        $this->assertTrue($valid);
    }

    public function testMinimumHtmlLinkIsValid()
    {
        $rule = new HtmlLinkRule('link', []);
        $valid = $rule->apply(HtmlLink::createFromArray([HtmlLink::PROPERTY_HREF => 'https://example.com']));
        $this->assertTrue($valid);
    }

    public function testNullByteRemoval()
    {
        $link_data = [
            HtmlLink::PROPERTY_HREF => "https://example.com/some\x00file",
            HtmlLink::PROPERTY_TEXT => "some\x00text",
            HtmlLink::PROPERTY_TITLE => "some\x00title",
            HtmlLink::PROPERTY_HREFLANG => "de-\x00at",
            HtmlLink::PROPERTY_TARGET => "_bla\x00nk",
            HtmlLink::PROPERTY_REL => "nofo\x00llow",
        ];

        $rule = new HtmlLinkRule('link', []);

        $valid = $rule->apply($link_data);

        $this->assertTrue($valid);

        $link = $rule->getSanitizedValue();

        $this->assertEquals("https://example.com/somefile", $link->getHref());
        $this->assertEquals("sometitle", $link->getTitle());
        $this->assertEquals("sometext", $link->getText());
        $this->assertEquals("de-at", $link->getHreflang());
        $this->assertEquals("_blank", $link->getTarget());
        $this->assertEquals("nofollow", $link->getRel());
    }

    public function testRemoveNewLine()
    {
        $link_data = [
            HtmlLink::PROPERTY_HREF => "https://www.foo.bar",
            HtmlLink::PROPERTY_TEXT => "some\t\ntext",
        ];

        $rule = new HtmlLinkRule('link', [
            HtmlLinkRule::OPTION_TEXT_ALLOW_CRLF => false,
            HtmlLinkRule::OPTION_TEXT_ALLOW_TAB => false
        ]);

        $valid = $rule->apply($link_data);

        $this->assertTrue($valid);
        $this->assertEquals("sometext", $rule->getSanitizedValue()->getText());
    }

    /**
     * @dataProvider provideValidValues
     */
    public function testAcceptanceOfValidValues($valid_value, $assert_message = '')
    {
        $rule = new HtmlLinkRule('link', []);
        $this->assertTrue($rule->apply($valid_value), $assert_message . ' should be accepted');
        $this->assertNotNull($rule->getSanitizedValue(), $assert_message . ' should not be null for a valid value');
    }

    public function provideValidValues()
    {
        return [
            [
                [
                    HtmlLink::PROPERTY_HREF => 'http://some.host/file.jpg'
                ],
                'link w/ only href'
            ],
            [
                [
                    HtmlLink::PROPERTY_HREF => 'https://example.com/file.jpg',
                    HtmlLink::PROPERTY_DOWNLOAD => true
                ],
                'link w/ only href and download'
            ],
            [
                [
                    HtmlLink::PROPERTY_TEXT => 'linktext',
                    HtmlLink::PROPERTY_HREF => 'http://example.com/some/path?q=foo#fragment'
                ],
                'link w/ text and complex href url'
            ],
            [
                [
                    HtmlLink::PROPERTY_TITLE => 'some title'
                ],
                'link w/ only title and no href should be valid as honeybee-cmf needs that atm, sry'
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testRejectionOfInvalidValues($invalid_value, $assert_message = '')
    {
        $rule = new HtmlLinkRule('scalar', []);
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
                    HtmlLink::PROPERTY_HREF => 'http://...example.com/some/path?q=foo#fragment'
                ],
                'link w/ invalid href url'
            ],
        ];
    }
}
