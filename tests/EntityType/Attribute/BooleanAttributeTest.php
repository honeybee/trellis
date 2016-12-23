<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\BooleanAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class BooleanAttributeTest extends TestCase
{
    public function testMakeValue(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $attribute = new BooleanAttribute('active', $entity_type);
        $this->assertTrue($attribute->makeValue(true)->toNative());
    }
}
