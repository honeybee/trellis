<?php

namespace Trellis\Tests\Fixtures;

use Trellis\Attribute\EntityList\EntityListAttribute;
use Trellis\Attribute\Text\TextAttribute;
use Trellis\Attribute\Uuid\UuidAttribute;
use Trellis\Entity\EntityType;

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
