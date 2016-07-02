<?php

namespace Trellis\Tests\Fixtures;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\Text\TextAttribute;
use Trellis\Attribute\Uuid\UuidAttribute;
use Trellis\Entity\EntityType;
use Trellis\Entity\EntityTypeInterface;

class ParagraphType extends EntityType
{
    public function __construct(EntityTypeInterface $parent, AttributeInterface $parent_attribute)
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
