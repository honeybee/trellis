<?php

namespace Trellis;

use Trellis\Entity\ValueObjectInterface;

interface EntityRelationInterface
{
    public function getRelatedIdentity(): ValueObjectInterface;
}
