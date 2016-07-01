<?php

namespace Trellis\Tests\Attribute;

use Trellis\Path\TypePath;
use Trellis\Path\TypePathParser;
use Trellis\Path\TypePathPart;
use Trellis\Tests\TestCase;

class TypePathParserTest extends TestCase
{
    /**
     * @dataProvider provideTypePathTestData
     */
    public function testTypePath($path_expression, $expected_length)
    {
        $type_path = TypePathParser::create()->parse($path_expression);

        $this->assertInstanceOf(TypePath::CLASS, $type_path);
        $this->assertCount($expected_length, $type_path);
        $this->assertEquals($path_expression, $type_path->__toString());
    }

    public function provideTypePathTestData()
    {
        return [
            [
                'path_expression' => 'content_objects',
                'expected_length' => 1
            ],
            [
                'path_expression' => 'content_objects.paragraph-title',
                'expected_length' => 2
            ],
            [
                'path_expression' => 'slideshows.teaser_slideshow-teasers.gallery_teaser-images',
                'expected_length' => 3
            ]
        ];
    }
}
