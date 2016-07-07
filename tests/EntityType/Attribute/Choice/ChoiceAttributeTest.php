<?php

namespace Trellis\Tests\EntityType\Attribute\Choice;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Choice\Choice;
use Trellis\EntityType\Attribute\Choice\ChoiceAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Entity\EntityInterface;
use Trellis\Tests\TestCase;

class ChoiceAttributeTest extends TestCase
{
    protected static $attr_options = [ 'allowed_values' => [ 'foo', 'bar', 'foobar' ] ];

    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $choice_attribute = new ChoiceAttribute('my_choice', $entity_type, self::$attr_options);

        $this->assertInstanceOf(AttributeInterface::CLASS, $choice_attribute);
        $this->assertEquals('my_choice', $choice_attribute->getName());
        $this->assertEquals($entity_type, $choice_attribute->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $choice_attribute = new ChoiceAttribute('my_choice', $entity_type, self::$attr_options);

        $this->assertInstanceOf(Choice::CLASS, $choice_attribute->createValue($entity));
        $this->assertInstanceOf(
            Choice::CLASS,
            $choice_attribute->createValue($entity, new Choice(self::$attr_options['allowed_values'], 'foo'))
        );
        $this->assertInstanceOf(Choice::CLASS, $choice_attribute->createValue($entity, 'foobar'));
    }
}
