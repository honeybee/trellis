<?php

namespace Trellis;

use Trellis\Entity\ValueObjectInterface;

interface EntityRelationInterface
{
    /**
     * @return ValueObjectInterface
     */
    public function getRelatedIdentity(): ValueObjectInterface;
}
