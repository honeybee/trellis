<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute\EntityRelationListAttribute;
use Trellis\EntityType\Params;
use Trellis\TypedEntityInterface;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\Attribute\NestedEntityListAttribute;
use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityType\Attribute\TextAttribute;
use Trellis\EntityType\EntityType;

final class ArticleType extends EntityType
{
    public function __construct()
    {
        $paragraph_params = [
            NestedEntityListAttribute::PARAM_TYPES => [ ParagraphType::CLASS, LocationType::CLASS ]
        ];
        $category_params = [
            EntityRelationListAttribute::PARAM_TYPES => [ CategoryRelationType::CLASS ]
        ];
        parent::__construct(
            "Article",
            new AttributeMap([
                new IntegerAttribute("id", $this),
                new TextAttribute("title", $this),
                new EntityRelationListAttribute("categories", $this, $category_params),
                new NestedEntityListAttribute("paragraphs", $this, $paragraph_params)
            ]),
            new Params([ "prefix" => "article" ])
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
        return new Article($this, $data, $parent);
    }
}
