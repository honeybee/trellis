<?php

namespace Trellis\Entity\ValueObject;

use Trellis\TypedEntityInterface;
use Trellis\Entity\ValueObjectListInterface;
use Trellis\Entity\ValueObjectList;

final class EntityList extends ValueObjectList
{
    /**
     * @param iterable|null|TypedEntityInterface[] $entities
     */
    public function __construct(iterable $entities = null)
    {
        parent::__construct(
            (function (TypedEntityInterface ...$entities): array {
                return $entities;
            })(...$entities ?? [])
        );
    }

    /**
     * @param ValueObjectListInterface $other_list
     *
     * @return ValueObjectListInterface
     */
    public function diff(ValueObjectListInterface $other_list): ValueObjectListInterface
    {
        $different_entities = [];
        /* @var TypedEntityInterface $entity */
        foreach ($this->internal_vector as $pos => $entity) {
            if (!$other_list->has($pos)) {
                $different_entities[] = $entity;
                continue;
            }
            /* @var TypedEntityInterface $other_entity */
            $other_entity = $other_list->get($pos);
            $diff = $entity->getValueObjectMap()->diff($other_entity->getValueObjectMap());
            if (!$diff->isEmpty()) {
                $different_entities[] = $entity;
            }
        }
        return new static($different_entities);
    }
}
