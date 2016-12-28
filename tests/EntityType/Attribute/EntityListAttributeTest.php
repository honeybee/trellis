<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\EntityListAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class EntityListAttributeTest extends TestCase
{
    /**
     * @expectedException \Trellis\Error\MissingImplementation
     */
    public function testInvalidType(): void
    {
        $params = [ EntityListAttribute::OPTION_TYPES => [ "\\Trellis\\FooBaR" ] ];
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        new EntityListAttribute("foo", $entity_type, $params);
    } // @codeCoverageIgnore
}
