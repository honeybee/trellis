<?php

namespace Trellis\Tests\Attribute\EntityList;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\EntityList\EntityListAttribute;
use Trellis\Entity\EntityTypeInterface;
use Trellis\Entity\EntityTypeMap;
use Trellis\Tests\Fixtures\ParagraphType;
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

    public function testGetTypeMap()
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
}
