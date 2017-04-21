<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\NestedEntity;
use Trellis\EntityTypeInterface;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;

final class NestedEntityTest extends TestCase
{
    private const FIXED_UUID = "941b4e51-e524-4e5d-8c17-1ef96585abc3";

    private const FIXED_DATA = [
        "id" => 42,
        "kicker" => "this is the kicker",
        "content" => "this is the content"
    ];

    /**
     * @var EntityTypeInterface $nestedEntityType
     */
    private $nestedEntityType;

    /**
     * @var NestedEntity $nestedEntity
     */
    private $nestedEntity;

    public function testEquals(): void
    {
        $equalEntity = $this->nestedEntityType->makeEntity(self::FIXED_DATA);
        $this->assertTrue($this->nestedEntity->equals($equalEntity));
        $unequalEntity = $equalEntity->withValue("kicker", "foobar");
        $this->assertFalse($this->nestedEntity->equals($unequalEntity));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue($this->nestedEntityType->makeEntity()->isEmpty());
        $this->assertFalse($this->nestedEntity->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals("Paragraph:42", (string)$this->nestedEntity);
    }

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testInvalidTypeComparison(): void
    {
        /* @var NestedEntity $differentEntity */
        $differentEntity = (new ArticleType)
            ->getAttribute("workshop_location")
            ->getValueType()
            ->get("location")
            ->makeEntity([ "id" => 23, "name" => "My POI"]);
        $this->nestedEntity->equals($differentEntity);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        $articleType = new ArticleType;
        $this->nestedEntityType = $articleType->getAttribute("paragraphs")
            ->getValueType()
            ->get("paragraph");
        $this->nestedEntity = $this->nestedEntityType->makeEntity(self::FIXED_DATA);
    }
}
