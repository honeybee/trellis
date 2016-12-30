<?php

namespace Trellis\Tests\EntityType;

use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\ParagraphType;
use Trellis\Tests\TestCase;
use Trellis\EntityType\EntityTypeMap;

class EntityTypeMapTest extends TestCase
{
    /**
     * @var EntityTypeMap $type_map
     */
    private $type_map;

    public function testHas(): void
    {
        $this->assertTrue($this->type_map->has("article"));
        $this->assertFalse($this->type_map->has("paragraph"));
    }

    public function testByName(): void
    {
        $this->assertInstanceOf(ArticleType::CLASS, $this->type_map->byName("Article"));
        $this->assertNull($this->type_map->byName("Paragraph"));
    }

    public function testByPrefix(): void
    {
        $this->assertInstanceOf(ArticleType::CLASS, $this->type_map->get("article"));
    }

    public function testByClass(): void
    {
        $this->assertInstanceOf(ArticleType::CLASS, $this->type_map->byClassName(ArticleType::CLASS));
        $this->assertNull($this->type_map->byClassName(ParagraphType::CLASS));
    }

    public function testCount(): void
    {
        $this->assertCount(1, $this->type_map);
    }

    public function testGetIterator(): void
    {
        $this->assertEquals(1, iterator_count($this->type_map));
    }

    protected function setUp(): void
    {
        $article_type = new ArticleType;
        $this->type_map = new EntityTypeMap([ $article_type ]);
    }
}
