<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\DecimalAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class DecimalAttributeTest extends TestCase
{
    public function testMakeValue(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $attribute = new DecimalAttribute('ratio', $entity_type);
        $this->assertEquals(23.42, $attribute->makeValue(23.42)->toNative());
    }
}
