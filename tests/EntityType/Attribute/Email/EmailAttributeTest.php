<?php

namespace Trellis\Tests\EntityType\Attribute\Email;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Email\Email;
use Trellis\EntityType\Attribute\Email\EmailAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Tests\TestCase;

class EmailAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $email_attribute = new EmailAttribute('my_email', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $email_attribute);
        $this->assertEquals('my_email', $email_attribute->getName());
        $this->assertEquals($entity_type, $email_attribute->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $email_attribute = new EmailAttribute('my_email', $entity_type);

        $this->assertInstanceOf(Email::CLASS, $email_attribute->createValue());
        $this->assertInstanceOf(Email::CLASS, $email_attribute->createValue(new Email('test@example.com')));
        $this->assertInstanceOf(Email::CLASS, $email_attribute->createValue('test@example.com'));
    }
}
