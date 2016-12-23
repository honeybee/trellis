<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\TextAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class TextAttributeTest extends TestCase
{
    public function testMakeValue(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $attribute = new TextAttribute('title', $entity_type);
        $this->assertEquals("hello world", $attribute->makeValue("hello world")->toNative());
    }
}
