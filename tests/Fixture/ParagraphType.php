<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\EntityType;
use Trellis\Entity\TypedEntityInterface;
use Trellis\ValueObject\Integer;
use Trellis\ValueObject\Text;

final class ParagraphType extends EntityType
{
    public function __construct(AttributeInterface $parentAttribute)
    {
        parent::__construct("Paragraph", [
            Attribute::define("id", $this, Integer::class),
            Attribute::define("kicker", $this, Text::class),
            Attribute::define("content", $this, Text::class)
        ], $parentAttribute);
    }

    /**
     * @inheritDoc
     */
    public function makeEntity(array $data = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        return Paragraph::fromNative($data, [ "entity_type" => $this, "parent" => $parent ]);
    }
}
