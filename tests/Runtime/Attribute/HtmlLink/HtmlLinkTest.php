<?php

namespace Trellis\Tests\Runtime\Attribute\HtmlLink;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLink;
use Trellis\Tests\TestCase;

class HtmlLinkTest extends TestCase
{
    public function testSimpleCreateSucceeds()
    {
        $link = new HtmlLink([
            HtmlLink::PROPERTY_HREF => 'http://some/file.jpg'
        ]);
        $this->assertEquals($link->getHref(), 'http://some/file.jpg');
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testSimpleCreateFailsWithEmptyString()
    {
        $link = new HtmlLink([ HtmlLink::PROPERTY_HREF => '' ]);
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testCreateWithoutArgumentsFails()
    {
        $link = new HtmlLink([]);
    }

    public function testComplexCreateSucceeds()
    {
        $link = new HtmlLink([
            HtmlLink::PROPERTY_HREF => 'http://some/file.jpg',
            HtmlLink::PROPERTY_TITLE => 'title',
            HtmlLink::PROPERTY_TEXT => 'caption',
            HtmlLink::PROPERTY_DOWNLOAD => false,
            HtmlLink::PROPERTY_HREFLANG => 'en-us',
            HtmlLink::PROPERTY_REL => 'nofollow',
            HtmlLink::PROPERTY_TARGET => '_blank',
        ]);

        $this->assertEquals('http://some/file.jpg', $link->getHref());
        $this->assertEquals('title', $link->getTitle());
        $this->assertEquals('caption', $link->getText());
        $this->assertFalse($link->getDownload());
        $this->assertEquals('en-us', $link->getHreflang());
        $this->assertEquals('nofollow', $link->getRel());
        $this->assertEquals('_blank', $link->getTarget());
    }

    public function testCreateFromPartialArraySucceeds()
    {
        $link = HtmlLink::createFromArray([
            HtmlLink::PROPERTY_HREF => 'http://some/file.jpg',
            HtmlLink::PROPERTY_TITLE => 'title',
            HtmlLink::PROPERTY_TEXT => 'text',
            HtmlLink::PROPERTY_DOWNLOAD => true,
        ]);

        $this->assertEquals('http://some/file.jpg', $link->getHref());
        $this->assertEquals('title', $link->getTitle());
        $this->assertEquals('text', $link->getText());
        $this->assertTrue($link->getDownload());
    }

    public function testComparisonOfTwoSimilarHtmlLinksSucceeds()
    {
        $other_img = new HtmlLink([
            HtmlLink::PROPERTY_HREF => 'http://some/other.png',
            HtmlLink::PROPERTY_TITLE => 'other_title'
        ]);

        $link = HtmlLink::createFromArray($other_img->toNative());

        $this->assertEquals('http://some/other.png', $link->getHref());
        $this->assertEquals('other_title', $link->getTitle());

        $this->assertTrue($link->similarTo($other_img));
    }

    public function testCreateWithSucceeds()
    {
        $link = new HtmlLink([
            HtmlLink::PROPERTY_HREF => 'http://some/other.png',
            HtmlLink::PROPERTY_TITLE => 'other_title'
        ]);

        $diff_link = $link->createWith([
            HtmlLink::PROPERTY_HREF => 'http://some/totally/other.png',
        ]);

        $this->assertEquals('http://some/totally/other.png', $diff_link->getHref());
        $this->assertEquals('other_title', $diff_link->getTitle());
        $this->assertFalse($link->similarTo($diff_link));
        $this->assertFalse($diff_link->similarTo($link));
    }

    public function testToArrayValuesEqualToNative()
    {
        $link = new HtmlLink([
            HtmlLink::PROPERTY_HREF => 'http://some/other.png',
            HtmlLink::PROPERTY_TITLE => 'other_title'
        ]);

        $a = $link->toArray();
        $b = $link->toNative();

        $this->assertEquals($a, $b);
    }
}
