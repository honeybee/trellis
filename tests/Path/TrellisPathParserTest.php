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
        $path_parts = TrellisPathParser::create()->parse('content_objects.1.title');

        $this->assertInstanceOf(TrellisPath::CLASS, $path_parts);
        $this->assertCount(2, $path_parts);
        $this->assertInstanceOf(ValuePathPart::CLASS, $path_parts[0]);
        $this->assertInstanceOf(AttributePathPart::CLASS, $path_parts[1]);
    }
}
