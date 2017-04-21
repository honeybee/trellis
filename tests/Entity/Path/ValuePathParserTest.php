<?php

namespace Trellis\Tests\Entity\Path;

use Trellis\Entity\Path\ValuePath;
use Trellis\Entity\Path\ValuePathParser;
use Trellis\Tests\TestCase;

class ValuePathParserTest extends TestCase
{
    /**
     * @dataProvider provideValuePathTestData
     * @param string $pathExpression
     * @param int $expectedLength
     */
    public function testTypePath(string $pathExpression, int $expectedLength): void
    {
        $typePath = ValuePathParser::create()->parse($pathExpression);

        $this->assertInstanceOf(ValuePath::CLASS, $typePath);
        $this->assertCount($expectedLength, $typePath);
        $this->assertEquals($pathExpression, $typePath->__toString());
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
                "pathExpression" => "paragraphs",
                "expectedLength" => 1
            ],
            [
                "pathExpression" => "paragraphs.1-title",
                "expectedLength" => 2
            ],
            [
                "pathExpression" => "slideshows.2-teasers.3-images",
                "expectedLength" => 3
            ]
        ];
    }
}
