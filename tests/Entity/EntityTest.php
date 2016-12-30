<?php

namespace Trellis\Tests\Entity;

use Trellis\EntityType\Attribute\NestedEntityListAttribute;
use Trellis\Tests\Fixture\Article;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\CategoryRelation;
use Trellis\Tests\Fixture\CategoryRelationType;
use Trellis\Tests\Fixture\Paragraph;
use Trellis\Tests\TestCase;

class EntityTest extends TestCase
{
    private const FIXED_DATA = [
        "@type" => "article",
        "title" => "Hello world!",
        "id" => 23,
        "categories" => [ [
            "@type" => "category_relation",
            "id" => 5,
            "related_id" => 23,
            "name" => "Sports"
        ] ],
        "paragraphs" => [ [
            "@type" => "paragraph",
            "id" => 42,
            "kicker" => "hey ho!",
            "content" => "this is the content!"
        ] ]
    ];

    /**
     * @var Article $entity
     */
    private $entity;

    public function testGetParent(): void
    {
        $article_type = $this->entity->getEntityType();
        /* @var NestedEntityListAttribute $paragraphs */
        $paragraphs = $article_type->getAttribute("paragraphs");
        $kicker_attr = $paragraphs->getEntityTypeMap()->get("paragraph")->getAttribute("kicker");
        $this->assertEquals($article_type, $kicker_attr->getParent()->getEntityType());
    }

    public function testGet(): void
    {
        $this->assertEquals(self::FIXED_DATA["id"], $this->entity->getIdentity()->toNative());
        $this->assertEquals(self::FIXED_DATA["title"], $this->entity->getTitle()->toNative());
        /* @var Paragraph $paragraph */
        $paragraph = $this->entity->get("paragraphs.0");
        $this->assertEquals(self::FIXED_DATA["paragraphs"][0]["id"], $paragraph->getIdentity()->toNative());
        $this->assertEquals(self::FIXED_DATA["paragraphs"][0]["kicker"], $paragraph->getKicker()->toNative());
        $this->assertEquals(self::FIXED_DATA["paragraphs"][0]["content"], $paragraph->getContent()->toNative());
    }

    public function testHas(): void
    {
        $this->assertTrue($this->entity->has("id"));
        $this->assertTrue($this->entity->has("title"));
        $this->assertTrue($this->entity->has("paragraphs"));
        $article = $this->entity->getEntityType()->makeEntity([ "id" => 25 ]);
        $this->assertFalse($article->has("title"));
    }

    public function testWithValue(): void
    {
        $article = $this->entity->withValue("id", 3);
        $this->assertEquals(self::FIXED_DATA["id"], $this->entity->get("id")->toNative());
        $this->assertEquals(3, $article->get("id")->toNative());
    }

    public function testDiff(): void
    {
        $article = (new ArticleType)->makeEntity([ "id" => 23, "title" => "Hello world!" ]);
        $diff_data = [
            "title" => "This is different",
            "paragraphs" => [ [
                "@type" => "paragraph",
                "id" => 42,
                "kicker" => "hey ho!",
                "content" => "this is the content!"
            ] ]
        ];
        $new_article = $article->withValues($diff_data);
        $calculated_diff = $new_article->getValueObjectMap()->diff($article->getValueObjectMap());
        $this->assertEquals($diff_data, $calculated_diff->toNative());
    }

    public function testIsSameAs(): void
    {
        $article_two = (new ArticleType)->makeEntity([ "id" => 23, "title" => "Hello world!" ]);
        // considered same, due to identifier
        $this->assertTrue($this->entity->isSameAs($article_two));
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testInvalidValue(): void
    {
        (new ArticleType)->makeEntity([ "id" => 23, "title" =>  [ 123 ] ]);
    } // @codeCoverageIgnore

    public function testGetEntityList(): void
    {
        $this->assertEquals("hey ho!", $this->entity->get("paragraphs.0-kicker")->toNative());
    }

    public function testToNative(): void
    {
        $this->assertEquals(self::FIXED_DATA, $this->entity->toNative());
    }

    public function testEntityRelation(): void
    {
        /* @var CategoryRelation $category_relation */
        $category_relation = $this->entity->getCategories()->getFirst();
        $this->assertEquals(23, $category_relation->getRelatedIdentity()->toNative());
        /* @var CategoryRelationType $relation_type */
        $relation_type = $category_relation->getEntityType();
        $this->assertEquals("Some\\Other\\RootEntity", $relation_type->getRelatedEntityTypeClass());
        $this->assertEquals("id", $relation_type->getRelatedAttributeName());
        $this->assertEquals(5, $category_relation->getIdentity()->toNative());
    }

    public function testRoot(): void
    {
        $article_type = new ArticleType;
        /* @var Article $article */
        $article = $article_type->makeEntity([
            "title" => "Hello world!",
            "id" => 23,
            "paragraphs" => [ [
                "@type" => "paragraph",
                "id" => 42,
                "kicker" => "hey ho!",
                "content" => "this is the content!"
            ] ]
        ]);
        /* @var Paragraph $paragraph */
        $paragraph = $article->getParagraphs()->getFirst();
        $this->assertTrue($article === $paragraph->getEntityRoot());
        $this->assertTrue($article_type === $paragraph->getEntityRoot()->getEntityType());
    }

    public function testToValuePath(): void
    {
        /* @var Article $article */
        $article = (new ArticleType)->makeEntity([
            "title" => "Hello world!",
            "id" => 23,
            "paragraphs" => [ [
                "@type" => "paragraph",
                "id" => 42,
                "kicker" => "hey ho!",
                "content" => "this is the content!"
            ] ]
        ]);
        /* @var Paragraph $paragraph */
        $paragraph = $article->getParagraphs()->getFirst();
        $this->assertEquals("paragraphs.0", $paragraph->toPath());
    }

    /**
     * @expectedException \Trellis\Error\UnknownAttribute
     */
    public function testInvalidHas(): void
    {
        $article = (new ArticleType)->makeEntity([ "id" => 23 ]);
        $article->has("foobar");
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\UnknownAttribute
     */
    public function testInvalidPath(): void
    {
        $article = (new ArticleType)->makeEntity([ "id" => 23 ]);
        $article->get("foo.0");
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        $this->entity = (new ArticleType)->makeEntity(self::FIXED_DATA);
    }
}
