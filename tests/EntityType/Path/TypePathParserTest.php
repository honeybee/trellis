<?php

namespace Trellis\Tests\EntityType\Path;

use Trellis\Tests\TestCase;
use Trellis\EntityType\Path\TypePath;
use Trellis\EntityType\Path\TypePathParser;

class TypePathParserTest extends TestCase
{
    /**
     * @dataProvider provideTypePathTestData
     * @param string $pathExpression
     * @param int $expectedLength
     */
    public function testTypePath(string $pathExpression, int $expectedLength): void
    {
        $typePath = TypePathParser::create()->parse($pathExpression);
        $this->assertInstanceOf(TypePath::class, $typePath);
        $this->assertCount($expectedLength, $typePath);
        $this->assertEquals($pathExpression, $typePath->__toString());
    }

    /**
     * @expectedException \Trellis\Error\InvalidTypePath
     */
    public function testMissingType(): void
    {
        TypePathParser::create()->parse("paragraphs.paragraph..");
    } // @codeCoverageIgnore


    /**
     * @expectedException \Trellis\Error\InvalidTypePath
     */
    public function testInvalidPath(): void
    {
        TypePathParser::create()->parse("paragraphs~");
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\InvalidTypePath
     */
    public function testMissingAttribute(): void
    {
        TypePathParser::create()->parse("paragraphs.paragraph");
    } // @codeCoverageIgnore

    /**
     * @codeCoverageIgnore
     * @return mixed[]
     */
    public function provideTypePathTestData(): array
    {
        return [
            [
                "pathExpression" => "paragraphs",
                "expectedLength" => 1
            ],
            [
                "pathExpression" => "paragraphs.paragraph-title",
                "expectedLength" => 2
            ],
            [
                "pathExpression" => "slideshows.teaser_slideshow-teasers.gallery_teaser-images",
                "expectedLength" => 3
            ]
        ];
    }
}
