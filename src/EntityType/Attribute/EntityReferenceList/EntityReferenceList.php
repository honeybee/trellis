<?php

namespace Trellis\EntityType\Attribute\EntityReferenceList;

use Trellis\EntityType\Attribute\EntityList\EntityList;

class EntityReferenceList extends EntityList
{
    public function __construct(array $entity_references = [])
    {
        parent::__construct($entity_references);
    }
}
