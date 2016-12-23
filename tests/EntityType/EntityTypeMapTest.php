<?php

namespace Trellis\Tests\EntityType;

use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\ParagraphType;
use Trellis\Tests\TestCase;
use Trellis\EntityType\EntityTypeMap;

class EntityTypeMapTest extends TestCase
{
    public function testHas(): void
    {
        $article_type = new ArticleType;
        $type_map = new EntityTypeMap([ $article_type ]);
        $this->assertTrue($type_map->has('article'));
        $this->assertFalse($type_map->has('paragraph'));
    }

    public function testByName(): void
    {
        $article_type = new ArticleType;
        $type_map = new EntityTypeMap([ $article_type ]);
        $this->assertEquals($article_type, $type_map->byName('Article'));
        $this->assertNull($type_map->byName('Paragraph'));
    }

    public function testByPrefix(): void
    {
        $article_type = new ArticleType;
        $type_map = new EntityTypeMap([ $article_type ]);
        $this->assertEquals($article_type, $type_map->get('article'));
    }

    public function testByClass(): void
    {
        $article_type = new ArticleType;
        $type_map = new EntityTypeMap([ $article_type ]);
        $this->assertEquals($article_type, $type_map->byClassName(ArticleType::CLASS));
        $this->assertNull($type_map->byClassName(ParagraphType::CLASS));
    }
}
