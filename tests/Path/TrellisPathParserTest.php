<?php

namespace Trellis\Tests\Attribute;

use Trellis\Path\AttributePathPart;
use Trellis\Path\TrellisPath;
use Trellis\Path\TrellisPathParser;
use Trellis\Path\ValuePathPart;
use Trellis\Tests\TestCase;

class TrellisPathParserTest extends TestCase
{
    public function testSimpleValuePath()
    {
        $path = TrellisPathParser::create()->parse('content_objects.1.title');

        $this->assertInstanceOf(TrellisPath::CLASS, $path);
        $this->assertCount(2, $path);
        $this->assertInstanceOf(ValuePathPart::CLASS, $path[0]);
        $this->assertInstanceOf(AttributePathPart::CLASS, $path[1]);
        $this->assertEquals('content_objects.1.title', $path->__toString());
    }

    public function testSimpleAttributePath()
    {
        $path = TrellisPathParser::create()->parse('content_objects.paragraph.title');

        $this->assertInstanceOf(TrellisPath::CLASS, $path);
        $this->assertCount(2, $path);
        $this->assertInstanceOf(AttributePathPart::CLASS, $path[0]);
        $this->assertInstanceOf(AttributePathPart::CLASS, $path[1]);
        $this->assertEquals('content_objects.paragraph.title', $path->__toString());
    }

    public function testValuePathLeadingToEntity()
    {
        $path = TrellisPathParser::create()->parse('content_objects.1.paragraphs.2');

        $this->assertInstanceOf(TrellisPath::CLASS, $path);
        $this->assertCount(2, $path);
        $this->assertInstanceOf(ValuePathPart::CLASS, $path->getLast());
        $this->assertEquals('content_objects.1.paragraphs.2', $path->__toString());
    }

    public function testValueDeepValuePath()
    {
        $path = TrellisPathParser::create()->parse('content_objects.1.paragraphs.2.title');

        $this->assertInstanceOf(TrellisPath::CLASS, $path);
        $this->assertCount(3, $path);
        $this->assertInstanceOf(ValuePathPart::CLASS, $path->getFirst());
        $this->assertInstanceOf(AttributePathPart::CLASS, $path->getLast());
        $this->assertEquals('content_objects.1.paragraphs.2.title', $path->__toString());
    }
}
