<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Text\TextAttribute;
use Trellis\EntityType\Attribute\Uuid\UuidAttribute;
use Trellis\EntityType\EntityType;

class ParagraphType extends EntityType
{
    public function __construct(AttributeInterface $parent_attribute)
    {
        parent::__construct(
            'Paragraph',
            [
                new UuidAttribute('uuid', $this),
                new TextAttribute('kicker', $this),
                new TextAttribute('content', $this)
            ],
            $parent_attribute
        );
    }

    public function getEntityImplementor()
    {
        return Paragraph::CLASS;
    }
}
