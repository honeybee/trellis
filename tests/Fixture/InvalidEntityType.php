<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute\Text\TextAttribute;
use Trellis\EntityType\Attribute\Uuid\UuidAttribute;
use Trellis\EntityType\EntityType;

class InvalidEntityType extends EntityType
{
    public function __construct()
    {
        parent::__construct('Article', [
            new UuidAttribute('uuid', $this),
            new TextAttribute('title', $this)
        ]);
    }

    public function getEntityImplementor()
    {
        return DoesntExist::CLASS;
    }
}
