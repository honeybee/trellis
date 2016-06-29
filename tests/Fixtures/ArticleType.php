<?php

namespace Trellis\Tests\Fixtures;

use Trellis\Attribute\TextAttribute;
use Trellis\Attribute\UuidAttribute;
use Trellis\Entity\EntityType;

class ArticleType extends EntityType
{
    public function __construct()
    {
        parent::__construct('Article', [
            'uuid' => new UuidAttribute('uuid', $this),
            'title' => new TextAttribute('title', $this)
        ]);
    }

    public function getEntityImplementor()
    {
        return Article::CLASS;
    }
}
