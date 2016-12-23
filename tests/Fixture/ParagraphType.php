<?php

namespace Trellis\Tests\Fixture;

use Trellis\DomainEntityInterface;
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
            null,
            $parent_attribute
        );
    }

    /**
     * @param array $data
     * @param null|DomainEntityInterface $parent
     *
     * @return DomainEntityInterface
     */
    public function makeEntity(array $data = [], DomainEntityInterface $parent = null): DomainEntityInterface
    {
        return new Paragraph($this, $data, $parent);
    }
}
