<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Params;
use Trellis\TypedEntityInterface;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityType\Attribute\TextAttribute;
use Trellis\EntityType\EntityType;

class ParagraphType extends EntityType
{
    public function __construct(AttributeInterface $parent_attribute)
    {
        parent::__construct(
            'Paragraph',
            new AttributeMap([
                new IntegerAttribute('id', $this),
                new TextAttribute('kicker', $this),
                new TextAttribute('content', $this)
            ]),
            new Params([ "prefix" => "paragraph" ]),
            $parent_attribute
        );
    }

    /**
     * @param array $data
     * @param null|TypedEntityInterface $parent
     *
     * @return TypedEntityInterface
     */
    public function makeEntity(array $data = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        return new Paragraph($this, $data, $parent);
    }
}
