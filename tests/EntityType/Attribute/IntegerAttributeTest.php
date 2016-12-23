<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class IntegerAttributeTest extends TestCase
{
    public function testMakeValue(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $attribute = new IntegerAttribute('number', $entity_type);
        $this->assertEquals(23, $attribute->makeValue(23)->toNative());
    }
}
