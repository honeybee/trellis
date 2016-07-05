<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityType\EntityTypeMap;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\ParagraphType;
use Trellis\Tests\TestCase;

class EntityTypeMapTest extends TestCase
{
    public function testByName()
    {
        $article_type = new ArticleType;
        $paragraph_type = new ParagraphType($article_type->getAttribute('content_objects'));

        $type_map = new EntityTypeMap([ 'article' => $article_type, 'paragraph' => $paragraph_type ]);

        $this->assertEquals($article_type, $type_map->byName('Article'));
        $this->assertEquals($paragraph_type, $type_map->byName('Paragraph'));
    }

    public function testByPrefix()
    {
        $article_type = new ArticleType;
        $paragraph_type = new ParagraphType($article_type->getAttribute('content_objects'));

        $type_map = new EntityTypeMap([ 'article' => $article_type, 'paragraph' => $paragraph_type ]);

        $this->assertEquals($article_type, $type_map->byPrefix('article'));
        $this->assertEquals($paragraph_type, $type_map->byPrefix('paragraph'));
    }

    public function testByClass()
    {
        $article_type = new ArticleType;
        $paragraph_type = new ParagraphType($article_type->getAttribute('content_objects'));

        $type_map = new EntityTypeMap([ 'article' => $article_type, 'paragraph' => $paragraph_type ]);

        $this->assertEquals($article_type, $type_map->byClassName(ArticleType::CLASS));
        $this->assertEquals($paragraph_type, $type_map->byClassName(ParagraphType::CLASS));
    }
}
