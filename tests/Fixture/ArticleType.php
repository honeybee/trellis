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
            Attribute::define("id", $this, Uuid::class),
            Attribute::define("created", $this, Timestamp::class),
            Attribute::define("title", $this, Text::class),
            Attribute::define("url", $this, Url::class),
            Attribute::define("feedback_mail", $this, Email::class),
            Attribute::define("average_voting", $this, Decimal::class),
            Attribute::define("workshop_date", $this, Date::class),
            Attribute::define("workshop_cancelled", $this, Boolean::class),
            NestedEntityAttribute::define("workshop_location", $this, [ LocationType::class ]),
            NestedEntityListAttribute::define("paragraphs", $this, [ ParagraphType::class ])
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
