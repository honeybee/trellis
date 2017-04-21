<?php

namespace Trellis\Tests\Entity;

use Trellis\EntityType\Attribute\NestedEntityListAttribute;
use Trellis\Entity\Path\ValuePath;
use Trellis\Tests\Fixture\Article;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\CategoryRelation;
use Trellis\Tests\Fixture\CategoryRelationType;
use Trellis\Tests\Fixture\Paragraph;
use Trellis\Tests\TestCase;

class EntityTest extends TestCase
{
    private const FIXED_UUID = "941b4e51-e524-4e5d-8c17-1ef96585abc3";

    private const FIXED_INPUT = [
        "@type" => "article",
        "id" => "525b4e51-e524-4e5d-8c17-1ef96585cbd3",
        "created" => "2017-04-02T23:42:05.000000+00:00",
        "title" => "hello world!",
        "url" => "http://www.example.com",
        "feedback_mail" => "info@example.com",
        "average_voting" => 23.42,
        "workshop_location" => [
            "@type" => "location",
            "id" => 42,
            "coords" => [ "lat" => 52.5119, "lon" => 13.3084 ]
        ],
        "workshop_date" => "2017-05-23",
        "workshop_cancelled" => true,
        "paragraphs" => [[
            "@type" => "paragraph",
            "id" => 23,
            "kicker" => "this is the kicker baby!",
            "content" => "hell yeah!"
        ]]
    ];

    private const EXPECTED_OUTPUT = [
        "@type" => "article",
        "id" => "525b4e51-e524-4e5d-8c17-1ef96585cbd3",
        "created" => "2017-04-02T23:42:05.000000+00:00",
        "title" => "hello world!",
        "url" => "http://www.example.com/",
        "feedback_mail" => "info@example.com",
        "average_voting" => 23.42,
        "workshop_location" => [
            "@type" => "location",
            "id" => 42,
            "coords" => [ "lat" => 52.5119, "lon" => 13.3084 ],
            "name" => "",
            "street" => "",
            "postal_code" => "",
            "city" => "",
            "country" => ""
        ],
        "workshop_date" => "2017-05-23",
        "workshop_cancelled" => true,
        "paragraphs" => [[
            "@type" => "paragraph",
            "id" => 23,
            "kicker" => "this is the kicker baby!",
            "content" => "hell yeah!"
        ]]
    ];

    /**
     * @var Article $entity
     */
    private $entity;

    public function testGetParent(): void
    {
        $articleType = $this->entity->getEntityType();
        /* @var NestedEntityListAttribute $paragraphs */
        $paragraphs = $articleType->getAttribute("paragraphs");
        $kickerAttr = $paragraphs->getValueType()->get("paragraph")->getAttribute("kicker");
        $this->assertEquals($articleType, $kickerAttr->getParent()->getEntityType());
    }

    public function testGet(): void
    {
        $this->assertEquals(self::FIXED_INPUT["id"], $this->entity->getIdentity()->toNative());
        $this->assertEquals(self::FIXED_INPUT["title"], $this->entity->getTitle()->toNative());
        /* @var Paragraph $paragraph */
        $paragraph = $this->entity->get("paragraphs.0");
        $this->assertEquals(self::FIXED_INPUT["paragraphs"][0]["id"], $paragraph->getIdentity()->toNative());
        $this->assertEquals(self::FIXED_INPUT["paragraphs"][0]["kicker"], $paragraph->getKicker()->toNative());
        $this->assertEquals(self::FIXED_INPUT["paragraphs"][0]["content"], $paragraph->getContent()->toNative());
    }

    public function testHas(): void
    {
        $this->assertTrue($this->entity->has("id"));
        $this->assertTrue($this->entity->has("title"));
        $this->assertTrue($this->entity->has("paragraphs"));
        $article = $this->entity->getEntityType()->makeEntity([ "id" => "941b4e51-e524-4e5d-8c17-1ef96585abc3" ]);
        $this->assertFalse($article->has("title"));
    }

    public function testWithValue(): void
    {
        $article = $this->entity->withValue("id", self::FIXED_UUID);
        $this->assertEquals(self::FIXED_INPUT["id"], $this->entity->get("id")->toNative());
        $this->assertEquals(self::FIXED_UUID, $article->get("id")->toNative());
    }

    public function testDiff(): void
    {
        $article = (new ArticleType)->makeEntity([
            "id" => self::FIXED_UUID,
            "title" => "Hello world!"
        ]);
        $diffData = [
            "title" => "This is different",
            "paragraphs" => [[
                "@type" => "paragraph",
                "id" => 42,
                "kicker" => "hey ho!",
                "content" => "this is the content!"
            ]]
        ];
        $newArticle = $article->withValues($diffData);
        $calculatedDiff = $newArticle->getValueObjectMap()->diff($article->getValueObjectMap());
        $this->assertEquals($diffData, $calculatedDiff->toArray());
    }

    public function testIsSameAs(): void
    {
        $articleTwo = (new ArticleType)->makeEntity([ "id" => self::FIXED_INPUT["id"], "title" => "Hello world!" ]);
        // considered same, due to identifier
        $this->assertTrue($this->entity->isSameAs($articleTwo));
    }

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testInvalidValue(): void
    {
        (new ArticleType)->makeEntity([ "id" => self::FIXED_UUID, "title" =>  [ 123 ] ]);
    } // @codeCoverageIgnore

    public function testGetEntityList(): void
    {
        $this->assertEquals(
            self::FIXED_INPUT["paragraphs"][0]["kicker"],
            $this->entity->get("paragraphs.0-kicker")->toNative()
        );
    }

    public function testToArray(): void
    {
        $this->assertEquals(self::EXPECTED_OUTPUT, $this->entity->toArray());
    }

    public function testRoot(): void
    {
        $articleType = new ArticleType;
        /* @var Article $article */
        $article = $articleType->makeEntity([
            "title" => "Hello world!",
            "id" => self::FIXED_UUID,
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
        $this->assertTrue($articleType === $paragraph->getEntityRoot()->getEntityType());
    }

    public function testToValuePath(): void
    {
        /* @var Article $article */
        $article = (new ArticleType)->makeEntity([
            "title" => "Hello world!",
            "id" => self::FIXED_UUID,
            "paragraphs" => [[
                "@type" => "paragraph",
                "id" => 42,
                "kicker" => "hey ho!",
                "content" => "this is the content!"
            ]]
        ]);
        /* @var Paragraph $paragraph */
        $paragraph = $article->getParagraphs()->getFirst();
        $this->assertEquals("paragraphs.0", (string)ValuePath::fromEntity($paragraph));
    }

    /**
     * @expectedException \Trellis\Error\UnknownAttribute
     */
    public function testInvalidHas(): void
    {
        $article = (new ArticleType)->makeEntity([ "id" => self::FIXED_UUID ]);
        $article->has("foobar");
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\UnknownAttribute
     */
    public function testInvalidPath(): void
    {
        $article = (new ArticleType)->makeEntity([ "id" => self::FIXED_UUID ]);
        $article->get("foo.0");
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        $this->entity = (new ArticleType)->makeEntity(self::FIXED_INPUT);
    }
}
