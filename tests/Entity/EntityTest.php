<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\EntityInterface;
use Trellis\Tests\Fixtures\ArticleType;
use Trellis\Tests\TestCase;

class EntityTest extends TestCase
{
    public function testConstruct()
    {
        $article_type = new ArticleType();
        $article = $article_type->createEntity();

        $this->assertInstanceOf(EntityInterface::CLASS, $article);
    }

    public function testGetValue()
    {
        $article_type = new ArticleType();
        $article = $article_type->createEntity([ 'title' => 'Hello world!' ]);

        $this->assertFalse($article->has('uuid'));
        $this->assertTrue($article->has('title'));
        $this->assertEquals('Hello world!', $article->getTitle()->toNative());
    }

    /**
     * @expectedException Assert\InvalidArgumentException
     */
    public function testInvalidValue()
    {
        $article_type = new ArticleType();
        $article_type->createEntity([ 'title' => 23 ]);
    }
}
