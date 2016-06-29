<?php

namespace Trellis\Tests\Attribute;

use Trellis\Path\AttributePathPart;
use Trellis\Path\TrellisPath;
use Trellis\Path\TrellisPathParser;
use Trellis\Path\ValuePathPart;
use Trellis\Tests\TestCase;

class TrellisPathParserTest extends TestCase
{
    public function testConstruct()
    {
        $path = TrellisPathParser::create()->parse('content_objects.1.title');

        $this->assertInstanceOf(TrellisPath::CLASS, $path);
        $this->assertCount(2, $path);
        $this->assertInstanceOf(ValuePathPart::CLASS, $path[0]);
        $this->assertInstanceOf(AttributePathPart::CLASS, $path[1]);
        $this->assertEquals('content_objects.1.title', $path->__toString());
    }
}
