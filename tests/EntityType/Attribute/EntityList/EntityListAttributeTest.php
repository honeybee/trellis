<?php

namespace Trellis\Tests\EntityType\Attribute\EntityList;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\EntityList\EntityList;
use Trellis\EntityType\Attribute\EntityList\EntityListAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\EntityType\EntityTypeMap;
use Trellis\Entity\EntityInterface;
use Trellis\Tests\Fixture\ParagraphType;
use Trellis\Tests\TestCase;

class EntityListAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity_list_attr = new EntityListAttribute('my_entity_list', $entity_type, [ 'entity_types' => [] ]);

        $this->assertInstanceOf(AttributeInterface::CLASS, $entity_list_attr);
        $this->assertEquals('my_entity_list', $entity_list_attr->getName());
        $this->assertEquals($entity_type, $entity_list_attr->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity_list_attr = new EntityListAttribute(
            'my_entity_list',
            $entity_type,
            [ 'entity_types' => [ 'paragraph' => ParagraphType::CLASS ] ]
        );

        $entity_list = $entity_list_attr->createValue($entity);
        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
        $entity_list = $entity_list_attr->createValue($entity, $this->getMockBuilder(EntityList::class)->getMock());
        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
        $entity_list = $entity_list_attr->createValue($entity, [
            [
                '@type' => 'paragraph',
                'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                'title' => 'foo'
            ]
        ]);
        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
    }

    public function testValidEntityTypesOptions()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity_list_attr = new EntityListAttribute(
            'my_entity_list',
            $entity_type,
            [ 'entity_types' => [ 'paragraph' => ParagraphType::CLASS ] ]
        );
        $type_map = $entity_list_attr->getEntityTypeMap();

        $this->assertInstanceOf(EntityTypeMap::CLASS, $type_map);
        $this->assertCount(1, $type_map);
        $this->assertInstanceOf(ParagraphType::CLASS, $type_map->byPrefix('paragraph'));
    }

    /**
     * @expectedException \Trellis\Exception
     */
    public function testMissingEntityTypesOption()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        new EntityListAttribute('my_entity_list', $entity_type);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Exception
     */
    public function testInvalidEntityTypesOption()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        new EntityListAttribute('my_entity_list', $entity_type, [ 'entity_types' => [ 'foo' => Bar::CLASS ] ]);
    } // @codeCoverageIgnore
}
