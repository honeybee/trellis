<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\EmailAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class EmailAttributeTest extends TestCase
{
    public function testMakeValue(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $attribute = new EmailAttribute("location", $entity_type);
        $this->assertEquals("peter.parker@example.com", $attribute->makeValue("peter.parker@example.com")->toNative());
    }
}
