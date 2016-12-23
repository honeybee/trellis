<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityInterface;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\Attribute\EntityListAttribute;
use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityType\Attribute\TextAttribute;
use Trellis\EntityType\EntityType;

final class ArticleType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Article',
            new AttributeMap([
                new IntegerAttribute('id', $this),
                new TextAttribute('title', $this),
                new EntityListAttribute('content_objects', $this, [
                    EntityListAttribute::OPTION_TYPES => [ ParagraphType::CLASS ]
                ])
            ])
        );
    }

    public function makeEntity(array $data = [], EntityInterface $parent = null): EntityInterface
    {
        return new Article($this, $data, $parent);
    }
}
