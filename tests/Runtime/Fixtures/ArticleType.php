<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Common\Options;
use Trellis\Runtime\Attribute\Boolean\BooleanAttribute;
use Trellis\Runtime\Attribute\Email\EmailAttribute;
use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Trellis\Runtime\Attribute\EntityReferenceList\EntityReferenceListAttribute;
use Trellis\Runtime\Attribute\Float\FloatAttribute;
use Trellis\Runtime\Attribute\GeoPoint\GeoPointAttribute;
use Trellis\Runtime\Attribute\ImageList\ImageListAttribute;
use Trellis\Runtime\Attribute\IntegerList\IntegerListAttribute;
use Trellis\Runtime\Attribute\Integer\IntegerAttribute;
use Trellis\Runtime\Attribute\KeyValueList\KeyValueListAttribute;
use Trellis\Runtime\Attribute\TextList\TextListAttribute;
use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Attribute\Timestamp\TimestampAttribute;
use Trellis\Runtime\Attribute\Url\UrlAttribute;
use Trellis\Runtime\Attribute\Uuid\UuidAttribute;
use Trellis\Runtime\EntityType;

class ArticleType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Article',
            [
                new UuidAttribute('uuid', $this),
                new TextAttribute('headline', $this, [ TextAttribute::OPTION_MIN_LENGTH => 4 ]),
                new TextAttribute('content', $this),
                new IntegerAttribute('click_count', $this),
                new FloatAttribute('float', $this),
                new GeoPointAttribute('coords', $this),
                new TextAttribute('author', $this),
                new EmailAttribute('email', $this),
                new UrlAttribute('website', $this),
                new TimestampAttribute(
                    'birthday',
                    $this,
                    [
                        TimestampAttribute::OPTION_DEFAULT_VALUE => '2015-01-29T09:18:28.534429+00:00'
                    ]
                ),
                new IntegerListAttribute('images', $this),
                new ImageListAttribute('thumbnails', $this),
                new TextListAttribute('keywords', $this),
                new BooleanAttribute('enabled', $this),
                new EmbeddedEntityListAttribute(
                    'content_objects',
                    $this,
                    [
                        EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ ParagraphType::CLASS ],
                        EmbeddedEntityListAttribute::OPTION_MIN_COUNT => 1
                    ]
                ),
                new EntityReferenceListAttribute(
                    'categories',
                    $this,
                    [
                        EntityReferenceListAttribute::OPTION_ENTITY_TYPES => [ ReferencedCategoryType::CLASS ]
                    ]
                ),
                new KeyValueListAttribute(
                    'meta',
                    $this,
                    [
                        KeyValueListAttribute::OPTION_VALUE_TYPE => KeyValueListAttribute::VALUE_TYPE_SCALAR,
                        KeyValueListAttribute::OPTION_MIN_COUNT => 1
                    ]
                ),
                new EmbeddedEntityListAttribute(
                    'workflow_state',
                    $this,
                    [
                        EmbeddedEntityListAttribute::OPTION_ENTITY_TYPES => [ WorkflowStateType::CLASS ]
                    ]
                )
            ],
            new Options(
                [
                    'foo' => 'bar',
                    'nested' => [
                        'foo' => 'bar',
                        'blah' => 'blub'
                    ]
                ]
            )
        );
    }

    public static function getEntityImplementor()
    {
        return Article::CLASS;
    }
}
