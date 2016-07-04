<?php

namespace Trellis\Tests\Entity\Path;

use Trellis\Entity\Path\ValuePath;
use Trellis\Entity\Path\ValuePathParser;
use Trellis\Tests\TestCase;

class ValuePathParserTest extends TestCase
{
    /**
     * @dataProvider provideValuePathTestData
     */
    public function testTypePath($path_expression, $expected_length)
    {
        $type_path = ValuePathParser::create()->parse($path_expression);

        $this->assertInstanceOf(ValuePath::CLASS, $type_path);
        $this->assertCount($expected_length, $type_path);
        $this->assertEquals($path_expression, $type_path->__toString());
    }

    public function provideValuePathTestData()
    {
        return [
            [
                'path_expression' => 'content_objects',
                'expected_length' => 1
            ],
            [
                'path_expression' => 'content_objects.1-title',
                'expected_length' => 2
            ],
            [
                'path_expression' => 'slideshows.2-teasers.3-images',
                'expected_length' => 3
            ]
        ];
    }
}