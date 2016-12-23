<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\DateAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class DateAttributeTest extends TestCase
{
    public function testMakeValue(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $attribute = new DateAttribute("date", $entity_type);
        $this->assertEquals("2016-07-04", $attribute->makeValue("2016-07-04")->toNative());
    }
}
