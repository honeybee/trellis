<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\EntityList;
use Trellis\EntityType\Attribute\EntityListAttribute;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\Paragraph;
use Trellis\Tests\Fixture\ParagraphType;
use Trellis\Tests\TestCase;

final class EntityListTest extends TestCase
{
    private const FIXED_PARAGRAPH = [
        "kicker" => "hey ho",
        "content" => "Foobar"
    ];

    /**
     * @var Paragraph $paragraph1
     */
    private $paragraph1;

    /**
     * @var Paragraph $paragraph2
     */
    private $paragraph2;

    public function testToNative(): void
    {
        $paragraphs = new EntityList([$this->paragraph1, $this->paragraph2]);
        $expected = [$this->paragraph1->toNative(), $this->paragraph2->toNative()];
        $this->assertEquals($expected, $paragraphs->toNative());
    }

    public function testEquals(): void
    {
        $paragraphs = new EntityList([$this->paragraph1, $this->paragraph2]);
        $same_list = new EntityList([$this->paragraph1, $this->paragraph2]);
        $this->assertTrue($paragraphs->equals($same_list));
        $different_list = new EntityList([$this->paragraph1, $this->paragraph1]);
        $this->assertFalse($paragraphs->equals($different_list));
        $empty_list = new EntityList;
        $this->assertFalse($paragraphs->equals($empty_list));
        $this->assertTrue($empty_list->equals(new EntityList));
    }

    public function testIsEmpty(): void
    {
        $paragraphs = new EntityList([$this->paragraph1]);
        $this->assertTrue((new EntityList)->isEmpty());
        $this->assertFalse($paragraphs->isEmpty());
    }

    public function testCount(): void
    {
        $this->assertCount(2, new EntityList([$this->paragraph1, $this->paragraph2]));
        $this->assertCount(0, new EntityList);
    }

    public function testGetIterator()
    {
        $this->assertEquals(2, iterator_count(new EntityList([$this->paragraph1, $this->paragraph2])));
    }

    public function testGetFirst(): void
    {
        $paragraphs = new EntityList([$this->paragraph1, $this->paragraph2]);
        $this->assertEquals($this->paragraph1, $paragraphs->getFirst());
    }

    public function testOffsetGet(): void
    {
        $paragraphs = new EntityList([$this->paragraph1, $this->paragraph2]);
        $this->assertEquals($this->paragraph1, $paragraphs[0]);
    }

    public function testOffsetExists(): void
    {
        $paragraphs = new EntityList([$this->paragraph1, $this->paragraph2]);
        $this->assertTrue(isset($paragraphs[1]));
    }

    /**
     * @expectedException \Trellis\Error\MutabilityError
     */
    public function testOffsetSetNotAllowed(): void
    {
        $paragraphs = new EntityList([$this->paragraph1, $this->paragraph2]);
        $paragraphs[1] = $this->paragraph1;
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\MutabilityError
     */
    public function testOffsetUnsetNotAllowed(): void
    {
        $paragraphs = new EntityList([$this->paragraph1, $this->paragraph2]);
        unset($paragraphs[1]);
    } // @codeCoverageIgnore

    public function testGetLast(): void
    {
        $paragraphs = new EntityList([ $this->paragraph1, $this->paragraph2 ]);
        $this->assertEquals($this->paragraph2, $paragraphs->getLast());
    }

    public function testDiff(): void
    {
        $paragraphs = new EntityList([ $this->paragraph1, $this->paragraph1 ]);
        $this->assertCount(1, $paragraphs->diff(new EntityList([ $this->paragraph1, $this->paragraph2 ])));
    }

    protected function setUp(): void
    {
        $article_type = new ArticleType;
        $article = $article_type->makeEntity();
        /* @var EntityListAttribute $content_objects */
        $content_objects = $article_type->getAttribute('content_objects');
        /* @var ParagraphType $paragraph_type */
        $paragraph_type = $content_objects->getEntityTypeMap()->get('paragraph');
        $this->paragraph1 = $paragraph_type->makeEntity(self::FIXED_PARAGRAPH, $article);
        $this->paragraph2 = $this->paragraph1->withValue('kicker', 'ho');
    }
}
