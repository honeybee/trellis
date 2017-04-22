<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\EntityType;
use Trellis\EntityType\NestedEntityAttribute;
use Trellis\EntityType\NestedEntityListAttribute;
use Trellis\Entity\TypedEntityInterface;
use Trellis\ValueObject\Boolean;
use Trellis\ValueObject\Date;
use Trellis\ValueObject\Decimal;
use Trellis\ValueObject\Email;
use Trellis\ValueObject\GeoPoint;
use Trellis\ValueObject\Integer;
use Trellis\ValueObject\Text;
use Trellis\ValueObject\Timestamp;
use Trellis\ValueObject\Url;
use Trellis\ValueObject\Uuid;

final class ArticleType extends EntityType
{
    public function __construct()
    {
        parent::__construct("Article", [
            Attribute::define("id", Uuid::class, $this),
            Attribute::define("created", Timestamp::class, $this),
            Attribute::define("title", Text::class, $this),
            Attribute::define("url", Url::class, $this),
            Attribute::define("feedback_mail", Email::class, $this),
            Attribute::define("average_voting", Decimal::class, $this),
            Attribute::define("workshop_date", Date::class, $this),
            Attribute::define("workshop_cancelled", Boolean::class, $this),
            NestedEntityAttribute::define("workshop_location", [ LocationType::class ], $this),
            NestedEntityListAttribute::define("paragraphs", [ ParagraphType::class ], $this)
        ]);
    }

    /**
     * @inheritDoc
     */
    public function makeEntity(array $entityState = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        $entityState["@type"] = $this;
        $entityState["@parent"] = $parent;
        return Article::fromArray($entityState);
    }
}
