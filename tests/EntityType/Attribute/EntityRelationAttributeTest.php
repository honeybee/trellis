<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Nil;
use Trellis\EntityType\Attribute\EntityRelationAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\Fixture\CategoryRelation;
use Trellis\Tests\Fixture\CategoryRelationType;
use Trellis\Tests\TestCase;

final class EntityRelationAttributeTest extends TestCase
{
    private const FIXED_DATA = [
        "@type" => "category_relation",
        "id" => 42,
        "related_id" => 5,
        "name" => "Sports"
    ];

    /**
     * @var EntityRelationAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue(self::FIXED_DATA)->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $category_rel = new CategoryRelation(
            $this->attribute->getEntityTypeMap()->get("category_relation"),
            self::FIXED_DATA
        );
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue($category_rel)->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertInstanceOf(Nil::CLASS, $this->attribute->makeValue());
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue(5);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\MissingImplementation
     */
    public function testNonExistingTypeClass(): void
    {
        $params = [ EntityRelationAttribute::PARAM_TYPES => [ "\\Trellis\\FooBaR" ] ];
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        new EntityRelationAttribute("foo", $entity_type, $params);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\CorruptValues
     */
    public function testInvalidType(): void
    {
        $data = self::FIXED_DATA;
        $data["@type"] = "foobar";
        $this->attribute->makeValue($data);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testMissingType(): void
    {
        $data = self::FIXED_DATA;
        unset($data["@type"]);
        $this->attribute->makeValue($data);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $params = [ EntityRelationAttribute::PARAM_TYPES => [ CategoryRelationType::CLASS ] ];
        $this->attribute = new EntityRelationAttribute("category", $entity_type, $params);
    }
}
