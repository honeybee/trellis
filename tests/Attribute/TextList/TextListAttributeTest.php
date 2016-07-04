<?php

namespace Trellis\Tests\Attribute\TextList;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\TextList\TextListAttribute;
use Trellis\Entity\EntityTypeInterface;
use Trellis\Tests\TestCase;

class TextListAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $text_list_attribute = new TextListAttribute('my_text_list', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $text_list_attribute);
        $this->assertEquals('my_text_list', $text_list_attribute->getName());
        $this->assertEquals($entity_type, $text_list_attribute->getEntityType());
    }
}
