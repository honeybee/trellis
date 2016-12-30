<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityType\Attribute\TextAttribute;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\EntityType;
use Trellis\EntityType\Params;
use Trellis\EntityTypeRelationInterface;
use Trellis\TypedEntityInterface;

final class CategoryRelationType extends EntityType implements EntityTypeRelationInterface
{
    /**
     * @param AttributeInterface $parent_attribute
     */
    public function __construct(AttributeInterface $parent_attribute)
    {
        parent::__construct(
            "CategoryRelation",
            new AttributeMap([
                new IntegerAttribute("id", $this),
                new IntegerAttribute("related_id", $this),
                new TextAttribute("name", $this),
            ]),
            new Params([ "prefix" => "category_relation" ]),
            $parent_attribute
        );
    }

    /**
     * @return string
     */
    public function getRelatedAttributeName(): string
    {
        return "id";
    }

    /**
     * @return string
     */
    public function getRelatedEntityTypeClass(): string
    {
        return "Some\\Other\\RootEntity";
    }

    /**
     * @param mixed[] $data Optional data for initial hydration.
     * @param TypedEntityInterface $parent
     *
     * @return TypedEntityInterface
     */
    public function makeEntity(array $data = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        return new CategoryRelation($this, $data, $parent);
    }
}
