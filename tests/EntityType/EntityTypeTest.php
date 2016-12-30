<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityTypeInterface;
use Trellis\Entity\ValueObject\Text;
use Trellis\Tests\Fixture\Article;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;
use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityType\Attribute\TextAttribute;

class EntityTypeTest extends TestCase
{
    /**
     * @var EntityTypeInterface $entity_type
     */
    private $entity_type;

    public function testGetName(): void
    {
        $this->assertEquals("Article", $this->entity_type->getName());
    }

    public function testGetPrefix(): void
    {
        $this->assertEquals("article", $this->entity_type->getPrefix());
    }

    public function testToTypePath(): void
    {
        $kicker_attr = $this->entity_type->getAttribute("paragraphs.paragraph-kicker");
        $this->assertEquals("paragraphs.paragraph-kicker", $kicker_attr->toPath());
    }

    public function testGetAttribute(): void
    {
        $this->assertInstanceOf(TextAttribute::CLASS, $this->entity_type->getAttribute("title"));
        $this->assertInstanceOf(IntegerAttribute::CLASS, $this->entity_type->getAttribute("id"));
    }

    public function testGetAttributes(): void
    {
        $this->assertCount(2, $this->entity_type->getAttributes([ "title", "id" ]));
        $this->assertCount(4, $this->entity_type->getAttributes());
    }

    public function testGetParent(): void
    {
        $paragraph_kicker = $this->entity_type->getAttribute("paragraphs.paragraph-kicker");
        $paragraph_type = $paragraph_kicker->getEntityType();
        $this->assertEquals($this->entity_type, $paragraph_type->getRoot());
        $this->assertEquals($this->entity_type, $paragraph_type->getParent());
        $this->assertTrue($paragraph_type->hasParent());
        $this->assertFalse($this->entity_type->hasParent());
        $this->assertTrue($this->entity_type->isRoot());
        $this->assertFalse($paragraph_type->isRoot());
    }

    public function testHasAttribute(): void
    {
        $this->assertTrue($this->entity_type->hasAttribute("title"));
        $this->assertTrue($this->entity_type->hasAttribute("paragraphs.paragraph-kicker"));
    }

    public function testMakeEntity(): void
    {
        /* @var Article $article */
        $article = $this->entity_type->makeEntity([
            "title" => "hello world!",
            "content" => "this is some test content ..."
        ]);
        $this->assertInstanceOf(Text::CLASS, $article->getTitle());
        $this->assertEquals("hello world!", $article->getTitle()->toNative());
    }

    public function testGetParam(): void
    {
        $this->assertEquals("article", $this->entity_type->getParam("prefix"));
    }

    public function testHasParam(): void
    {
        $this->assertTrue($this->entity_type->hasParam("prefix"));
        $this->assertFalse($this->entity_type->hasParam("foobar"));
    }

    /**
     * @expectedException \Trellis\Error\InvalidType
     */
    public function testGetAttributeWithNonExistingAttribute(): void
    {
        $this->entity_type->getAttribute("foobar");
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        $this->entity_type = new ArticleType;
    }
}
