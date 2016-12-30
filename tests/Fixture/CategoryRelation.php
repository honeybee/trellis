<?php

namespace Trellis\Tests\Fixture;

use Trellis\Entity\NestedEntity;
use Trellis\Entity\ValueObject\Integer;
use Trellis\Entity\ValueObjectInterface;
use Trellis\EntityRelationInterface;
use Trellis\EntityTypeRelationInterface;

final class CategoryRelation extends NestedEntity implements EntityRelationInterface
{
    /**
     * @return ValueObjectInterface
     */
    public function getIdentity(): ValueObjectInterface
    {
        return $this->getId();
    }

    /**
     * @return Integer
     */
    public function getId(): Integer
    {
        return $this->get("id");
    }

    /**
     * @return ValueObjectInterface
     */
    public function getRelatedIdentity(): ValueObjectInterface
    {
        return $this->get("related_id");
    }
}
