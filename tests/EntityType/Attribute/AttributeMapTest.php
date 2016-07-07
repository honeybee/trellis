<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Text\TextAttribute;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;

class AttributeMapTest extends TestCase
{
    public function testByClassNames()
    {
        $entity_type = new ArticleType;

        $attr_map = $entity_type->getAttributes();
        $this->assertCount(1, $attr_map->byClassNames([ TextAttribute::CLASS ]));
    }

    public function testCollate()
    {
        $entity_type = new ArticleType;

        $attr_map = $entity_type->getAttributes();
        $text_attr_map = $attr_map->collate(function (AttributeInterface $attribute) {
            return $attribute instanceof TextAttribute;
        });

        $this->assertCount(3, $text_attr_map);
    }
}
