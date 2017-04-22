<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\NestedEntityList;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;
use Trellis\ValueObject\Text;

final class NestedEntityListTest extends TestCase
{
    private const FIXED_PARAGRAPH = [
        "id" => 42,
        "kicker" => "hey ho",
        "content" => "Foobar"
    ];

    /**
     * @var Paragraph
     */
    private $paragraph1;

    /**
     * @var Paragraph
     */
    private $paragraph2;

    /**
     * @var NestedEntityList
     */
    private $entityList;

    public function testToNative(): void
    {
        $expected = [ $this->paragraph1->toNative(), $this->paragraph2->toNative() ];
        $this->assertEquals($expected, $this->entityList->toNative());
    }

    public function testEquals(): void
    {
        $sameList = new NestedEntityList([ $this->paragraph1, $this->paragraph2 ]);
        $this->assertTrue($this->entityList->equals($sameList));
        $differentList = new NestedEntityList([ $this->paragraph1, $this->paragraph1 ]);
        $this->assertFalse($this->entityList->equals($differentList));
        $emptyList = new NestedEntityList;
        $this->assertFalse($this->entityList->equals($emptyList));
        $this->assertTrue($emptyList->equals(new NestedEntityList));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new NestedEntityList)->isEmpty());
        $this->assertFalse($this->entityList->isEmpty());
    }

    public function testCount(): void
    {
        $this->assertCount(2, $this->entityList);
        $this->assertCount(0, new NestedEntityList);
    }

    public function testGetIterator()
    {
        $this->assertEquals(2, iterator_count($this->entityList));
    }

    public function testGetFirst(): void
    {
        $this->assertEquals($this->paragraph1, $this->entityList->getFirst());
    }

    public function testPush(): void
    {
        $entityList = (new NestedEntityList)
            ->push($this->entityList->getFirst())
            ->push($this->entityList->getFirst());
        $this->assertCount(2, $entityList);
    }

    public function testRemove(): void
    {
        $this->assertCount(1, $this->entityList->remove($this->entityList->get(1)));
    }

    public function testGetLast(): void
    {
        $this->assertEquals($this->paragraph2, $this->entityList->getLast());
    }

    public function testDiff(): void
    {
        $this->assertCount(1, $this->entityList->diff(new NestedEntityList([ $this->paragraph1 ])));
    }

    public function testToString(): void
    {
        $this->assertEquals("Paragraph:42,\nParagraph:5", (string)$this->entityList);
    }

    /**
     * @expectedException \Trellis\Error\InvalidType
     */
    public function testIncompatibleTypeComparison(): void
    {
        $this->entityList->equals(Text::fromNative("wont work"));
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        $articleType = new ArticleType;
        $article = $articleType->makeEntity();
        /* @var NestedEntityListAttribute $paragraphs */
        $paragraphs = $articleType->getAttribute("paragraphs");
        /* @var ParagraphType $paragraphType */
        $paragraphType = $paragraphs->getValueType()->get("paragraph");
        $this->paragraph1 = $paragraphType->makeEntity(self::FIXED_PARAGRAPH, $article);
        $this->paragraph2 = $this->paragraph1->withValue("kicker", "ho")->withValue("id", 5);
        $this->entityList = new NestedEntityList([$this->paragraph1, $this->paragraph2]);
    }
}
