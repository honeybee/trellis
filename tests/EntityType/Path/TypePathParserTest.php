<?php

namespace Trellis\Tests\EntityType\Path;

use Trellis\Tests\TestCase;
use Trellis\EntityType\Path\TypePath;
use Trellis\EntityType\Path\TypePathParser;

class TypePathParserTest extends TestCase
{
    /**
     * @dataProvider provideTypePathTestData
     * @param string $path_expression
     * @param int $expected_length
     */
    public function testTypePath(string $path_expression, int $expected_length): void
    {
        $type_path = TypePathParser::create()->parse($path_expression);
        $this->assertInstanceOf(TypePath::CLASS, $type_path);
        $this->assertCount($expected_length, $type_path);
        $this->assertEquals($path_expression, $type_path->__toString());
    }

    /**
     * @expectedException \Trellis\Error\InvalidTypePath
     */
    public function testMissingType(): void
    {
        TypePathParser::create()->parse('content_objects.paragraph..');
    } // @codeCoverageIgnore


    /**
     * @expectedException \Trellis\Error\InvalidTypePath
     */
    public function testInvalidPath(): void
    {
        TypePathParser::create()->parse('content_objects~');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\InvalidTypePath
     */
    public function testMissingAttribute(): void
    {
        TypePathParser::create()->parse('content_objects.paragraph');
    } // @codeCoverageIgnore

    /**
     * @codeCoverageIgnore
     * @return mixed[]
     */
    public function provideTypePathTestData(): array
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
