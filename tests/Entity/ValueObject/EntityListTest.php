<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\EntityList;
use Trellis\Entity\ValueObject\Text;
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

    /**
     * @var EntityList $entity_list
     */
    private $entity_list;

    public function testToNative(): void
    {
        $expected = [ $this->paragraph1->toNative(), $this->paragraph2->toNative() ];
        $this->assertEquals($expected, $this->entity_list->toNative());
    }

    public function testEquals(): void
    {
        $same_list = new EntityList([$this->paragraph1, $this->paragraph2]);
        $this->assertTrue($this->entity_list->equals($same_list));
        $different_list = new EntityList([$this->paragraph1, $this->paragraph1]);
        $this->assertFalse($this->entity_list->equals($different_list));
        $empty_list = new EntityList;
        $this->assertFalse($this->entity_list->equals($empty_list));
        $this->assertTrue($empty_list->equals(new EntityList));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new EntityList)->isEmpty());
        $this->assertFalse($this->entity_list->isEmpty());
    }

    public function testCount(): void
    {
        $this->assertCount(2, $this->entity_list);
        $this->assertCount(0, new EntityList);
    }

    public function testGetIterator()
    {
        $this->assertEquals(2, iterator_count($this->entity_list));
    }

    public function testGetFirst(): void
    {
        $this->assertEquals($this->paragraph1, $this->entity_list->getFirst());
    }

    public function testAdd(): void
    {
        $entity_list = (new EntityList)
            ->add($this->entity_list[0])
            ->add($this->entity_list[0]);
        $this->assertCount(2, $entity_list);
    }

    public function testRemove(): void
    {
        $this->assertCount(1, $this->entity_list->remove($this->entity_list[1]));
    }

    public function testOffsetGet(): void
    {
        $this->assertEquals($this->paragraph1, $this->entity_list[0]);
    }

    public function testOffsetExists(): void
    {
        $this->assertTrue(isset($this->entity_list[1]));
    }

    /**
     * @expectedException \Trellis\Error\MutabilityError
     */
    public function testOffsetSetNotAllowed(): void
    {
        $this->entity_list[1] = $this->paragraph1;
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\MutabilityError
     */
    public function testOffsetUnsetNotAllowed(): void
    {
        unset($this->entity_list[1]);
    } // @codeCoverageIgnore

    public function testGetLast(): void
    {
        $this->assertEquals($this->paragraph2, $this->entity_list->getLast());
    }

    public function testDiff(): void
    {
        $this->assertCount(1, $this->entity_list->diff(new EntityList([ $this->paragraph1 ])));
    }

    /**
     * @expectedException \Trellis\Error\InvalidType
     */
    public function testIncompatibleTypeComparison(): void
    {
        $this->entity_list->equals(new Text("wont work"));
    } // @codeCoverageIgnore

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
        $this->entity_list = new EntityList([$this->paragraph1, $this->paragraph2]);
    }
}
