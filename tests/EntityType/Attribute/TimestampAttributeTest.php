<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\TimestampAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class TimestampAttributeTest extends TestCase
{
    public function testMakeValue(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $attribute = new TimestampAttribute("timestamp", $entity_type);
        $this->assertEquals(
            "2016-07-04T19:27:07.000000+02:00",
            $attribute->makeValue("2016-07-04T19:27:07.000000+02:00")->toNative()
        );
    }
}
