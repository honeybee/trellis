<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityType\Attribute;
use Trellis\EntityType\Path\TypePath;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;
use Trellis\ValueObject\Text;

class EntityTypeTest extends TestCase
{
    /**
     * @var EntityTypeInterface $entityType
     */
    private $entityType;

    public function testGetName(): void
    {
        $this->assertEquals("Article", $this->entityType->getName());
    }

    public function testGetPrefix(): void
    {
        $this->assertEquals("article", $this->entityType->getPrefix());
    }

    public function testToTypePath(): void
    {
        $kickerAttr = $this->entityType->getAttribute("paragraphs.paragraph-kicker");
        $this->assertEquals("paragraphs.paragraph-kicker", (string)TypePath::fromAttribute($kickerAttr));
    }

    public function testGetAttribute(): void
    {
        $this->assertInstanceOf(Attribute::class, $this->entityType->getAttribute("title"));
        $this->assertInstanceOf(Attribute::class, $this->entityType->getAttribute("id"));
    }

    public function testGetAttributes(): void
    {
        $this->assertCount(2, $this->entityType->getAttributes([ "title", "id" ]));
        $this->assertCount(10, $this->entityType->getAttributes());
    }

    public function testGetParent(): void
    {
        $paragraphKicker = $this->entityType->getAttribute("paragraphs.paragraph-kicker");
        $paragraphType = $paragraphKicker->getEntityType();
        $this->assertEquals($this->entityType, $paragraphType->getRoot());
        $this->assertEquals($this->entityType, $paragraphType->getParent());
        $this->assertTrue($paragraphType->hasParent());
        $this->assertFalse($this->entityType->hasParent());
        $this->assertTrue($this->entityType->isRoot());
        $this->assertFalse($paragraphType->isRoot());
    }

    public function testHasAttribute(): void
    {
        $this->assertTrue($this->entityType->hasAttribute("title"));
        $this->assertTrue($this->entityType->hasAttribute("paragraphs.paragraph-kicker"));
    }

    public function testMakeEntity(): void
    {
        /* @var Article $article */
        $article = $this->entityType->makeEntity([
            "title" => "hello world!",
            "content" => "this is some test content ..."
        ]);
        $this->assertInstanceOf(Text::class, $article->getTitle());
        $this->assertEquals("hello world!", $article->getTitle()->toNative());
    }

    /**
     * @expectedException \Trellis\Error\InvalidType
     */
    public function testGetAttributeWithNonExistingAttribute(): void
    {
        $this->entityType->getAttribute("foobar");
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        $this->entityType = new ArticleType;
    }
}
