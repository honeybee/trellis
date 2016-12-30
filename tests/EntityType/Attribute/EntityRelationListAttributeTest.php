<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\EntityRelationList;
use Trellis\EntityInterface;
use Trellis\EntityType\Attribute\EntityRelationListAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\Fixture\CategoryRelation;
use Trellis\Tests\Fixture\CategoryRelationType;
use Trellis\Tests\TestCase;

final class EntityRelationListAttributeTest extends TestCase
{
    private const FIXED_DATA = [ [
        "@type" => "category_relation",
        "id" => 42,
        "related_id" => 5,
        "name" => "Sports"
    ] ];

    /**
     * @var EntityRelationListAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue(self::FIXED_DATA)->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $parent = $this->getMockBuilder(EntityInterface::CLASS)->getMock();
        $category_rel_type = $this->attribute->getEntityTypeMap()->get("category_relation");
        $categories = new EntityRelationList([ new CategoryRelation($category_rel_type, self::FIXED_DATA[0])], $parent);
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue($categories)->toNative());
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue("snafu!");
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $params = [ EntityRelationListAttribute::PARAM_TYPES => [ CategoryRelationType::CLASS ] ];
        $this->attribute = new EntityRelationListAttribute("categories", $entity_type, $params);
    }
}
