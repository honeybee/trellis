<?php

namespace Trellis\Entity;

use Trellis\EntityType\EntityTypeInterface;
use Trellis\ValueObject\ValueObjectInterface;

interface TypedEntityInterface extends EntityInterface
{
    public const ENTITY_TYPE = "@type";

    /**
     * @return ValueObjectMap
     */
    public function getValueObjectMap(): ValueObjectMap;

    /**
     * Returns the entity"s root, if it has one.
     * @return TypedEntityInterface
     */
    public function getEntityRoot(): TypedEntityInterface;

    /**
     * Returns the entity"s parent, if it has one.
     * @return TypedEntityInterface|null
     */
    public function getEntityParent(): ?TypedEntityInterface;

    /**
     * Returns the entity"s type.
     * @return EntityTypeInterface
     */
    public function getEntityType(): EntityTypeInterface;
}
