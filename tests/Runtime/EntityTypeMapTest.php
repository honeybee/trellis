<?php

namespace Trellis\Tests\Runtime;

use Trellis\Tests\TestCase;
use Trellis\Runtime\EntityTypeMap;
use Trellis\Tests\Runtime\Fixtures\ArticleType;

class EntityTypeMapTest extends TestCase
{
    public function testCreateEntityTypeMap()
    {
        $map = new EntityTypeMap;

        $this->assertInstanceOf(EntityTypeMap::CLASS, $map);
    }

    public function testSetEntityInEmptyMap()
    {
        $article_type = new ArticleType;
        $map = new EntityTypeMap;

        $map->setItem('article_key', $article_type);

        $map_article_type = $map->getItem('article_key');
        $this->assertEquals($article_type, $map_article_type);
    }

    public function testGetByClassName()
    {
        $article_fqcn = '\Trellis\Tests\Runtime\Fixtures\ArticleType';
        $article_type = new ArticleType;
        $map = new EntityTypeMap;

        $map->setItem('article_key', $article_type);

        $map_article_type = $map->getByClassName($article_fqcn);
        $this->assertEquals($article_type, $map_article_type);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testGetByClassNameNotFound()
    {
        $article_fqcn = '\Trellis\Tests\Runtime\Fixtures\UnexpectedArticleType';
        $article_type = new ArticleType;
        $map = new EntityTypeMap;

        $map->setItem('article_key', $article_type);

        $map_article_type = $map->getByClassName($article_fqcn);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testGetByClassNameFoundMoreThanOne()
    {
        $article_fqcn = '\Trellis\Tests\Runtime\Fixtures\ArticleType';
        $article_type = new ArticleType;
        $map = new EntityTypeMap;

        $map->setItem('article_key', $article_type);
        $map->setItem('another_article_key', $article_type);

        $map_article_type = $map->getByClassName($article_fqcn);
    }

    public function testGetByEntityImplementor()
    {
        $article_impl = '\Trellis\Tests\Runtime\Fixtures\Article';
        $article_type = new ArticleType;
        $map = new EntityTypeMap;

        $map->setItem('article_key', $article_type);

        $map_article_type = $map->getByEntityImplementor($article_impl);
        $this->assertEquals($article_type, $map_article_type);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testGetByEntityImplementorNotFound()
    {
        $article_fqcn = '\Trellis\Tests\Runtime\Fixtures\UnexpectedArticle';
        $article_type = new ArticleType;
        $map = new EntityTypeMap;

        $map->setItem('article_key', $article_type);

        $map_article_type = $map->getByEntityImplementor($article_fqcn);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testGetByEntityImplementorFoundMoreThanOne()
    {
        $article_fqcn = '\Trellis\Tests\Runtime\Fixtures\Article';
        $article_type = new ArticleType;
        $map = new EntityTypeMap;

        $map->setItem('article_key', $article_type);
        $map->setItem('another_article_key', $article_type);

        $map_article_type = $map->getByEntityImplementor($article_fqcn);
    }
}
