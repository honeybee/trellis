<?php

namespace Trellis\Tests\Entity\Path;

use Trellis\Entity\Path\ValuePath;
use Trellis\Entity\Path\ValuePathParser;
use Trellis\Tests\TestCase;

class ValuePathParserTest extends TestCase
{
    /**
     * @dataProvider provideValuePathTestData
     * @param string $path_expression
     * @param int $expected_length
     */
    public function testTypePath(string $path_expression, int $expected_length): void
    {
        $type_path = ValuePathParser::create()->parse($path_expression);

        $this->assertInstanceOf(ValuePath::CLASS, $type_path);
        $this->assertCount($expected_length, $type_path);
        $this->assertEquals($path_expression, $type_path->__toString());
    }

    /**
     * @expectedException \Trellis\Error\InvalidValuePath
     */
    public function testInvalidPath(): void
    {
        ValuePathParser::create()->parse("2-teasers");
    } // @codeCoverageIgnore

    /**
     * @codeCoverageIgnore
     * @return mixed[]
     */
    public function provideValuePathTestData(): array
    {
        return [
            [
                "path_expression" => "paragraphs",
                "expected_length" => 1
            ],
            [
                "path_expression" => "paragraphs.1-title",
                "expected_length" => 2
            ],
            [
                "path_expression" => "slideshows.2-teasers.3-images",
                "expected_length" => 3
            ]
        ];
    }
}
