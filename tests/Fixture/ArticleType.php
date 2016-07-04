<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute\EntityList\EntityListAttribute;
use Trellis\EntityType\Attribute\Text\TextAttribute;
use Trellis\EntityType\Attribute\Uuid\UuidAttribute;
use Trellis\EntityType\EntityType;

class ArticleType extends EntityType
{
    public function __construct()
    {
        parent::__construct('Article', [
            new UuidAttribute('uuid', $this),
            new TextAttribute('title', $this),
            new EntityListAttribute(
                'content_objects',
                $this,
                [ 'entity_types' => [ ParagraphType::CLASS ] ]
            )
        ]);
    }

    public function getEntityImplementor()
    {
        return Article::CLASS;
    }
}
