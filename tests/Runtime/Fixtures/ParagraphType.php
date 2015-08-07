<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Common\Options;
use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Attribute\GeoPoint\GeoPointAttribute;
use Trellis\Runtime\Attribute\Textarea\TextareaAttribute;
use Trellis\Runtime\EntityType;
use Trellis\Runtime\EntityTypeInterface;

class ParagraphType extends EntityType
{
    public function __construct(EntityTypeInterface $parent, AttributeInterface $parent_attribute)
    {
        parent::__construct(
            'Paragraph',
            [
                new TextAttribute('title', $this, [], $parent_attribute),
                new TextareaAttribute('content', $this, [], $parent_attribute),
                new GeoPointAttribute('coords', $this, [], $parent_attribute),
            ],
            new Options(
                [
                    'foo' => 'bar',
                    'nested' => [
                        'foo' => 'bar',
                        'blah' => 'blub'
                    ]
                ]
            ),
            $parent,
            $parent_attribute
        );
    }

    public static function getEntityImplementor()
    {
        return Paragraph::CLASS;
    }
}
