<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\NestedEntity;
use Trellis\EntityTypeInterface;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;

final class NestedEntityTest extends TestCase
{
    private const FIXED_DATA = [
        "id" => 42,
        "kicker" => "this is the kicker",
        "content" => "this is the content"
    ];

    /**
     * @var EntityTypeInterface $nested_entity_type
     */
    private $nested_entity_type;

    /**
     * @var NestedEntity $nested_entity
     */
    private $nested_entity;

    public function testEquals(): void
    {
        $equal_entity = $this->nested_entity_type->makeEntity(self::FIXED_DATA);
        $this->assertTrue($this->nested_entity->equals($equal_entity));
        $unequal_entity = $equal_entity->withValue("kicker", "foobar");
        $this->assertFalse($this->nested_entity->equals($unequal_entity));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue($this->nested_entity_type->makeEntity()->isEmpty());
        $this->assertFalse($this->nested_entity->isEmpty());
    }

    /**
     * @expectedException \Trellis\Error\InvalidType
     */
    public function testInvalidTypeComparison(): void
    {
        /* @var NestedEntity $different_entity */
        $different_entity = (new ArticleType)
            ->getAttribute("content_objects")
            ->getEntityTypeMap()
            ->get("location")
            ->makeEntity([ "id" => 23, "name" => "My POI"]);
        $this->nested_entity->equals($different_entity);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        $article_type = new ArticleType;
        $this->nested_entity_type = $article_type->getAttribute("content_objects")
            ->getEntityTypeMap()
            ->get("paragraph");
        $this->nested_entity = $this->nested_entity_type->makeEntity(self::FIXED_DATA);
    }
}
