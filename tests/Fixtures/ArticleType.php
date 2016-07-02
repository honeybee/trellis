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
            'uuid' => new UuidAttribute('uuid', $this),
            'title' => new TextAttribute('title', $this),
            'content_objects' => new EntityListAttribute(
                'content_objects',
                $this,
                [ ParagraphType::CLASS ]
            )
        ]);
    }

    public function getEntityImplementor()
    {
        return Article::CLASS;
    }
}
