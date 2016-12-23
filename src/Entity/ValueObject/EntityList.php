<?php

namespace Trellis\Entity\ValueObject;

use Trellis\DomainEntityInterface;
use Trellis\Entity\ValueObjectListInterface;
use Trellis\Entity\ValueObjectList;

final class EntityList extends ValueObjectList implements ValueObjectListInterface
{
    /**
     * @param iterable|null|DomainEntityInterface[] $entities
     */
    public function __construct(iterable $entities = null)
    {
        (function (DomainEntityInterface ...$entities): void {
            parent::__construct($entities);
        })(...$entities ?? []);
    }

    /**
     * @param ValueObjectListInterface $other_list
     *
     * @return ValueObjectListInterface
     */
    public function diff(ValueObjectListInterface $other_list): ValueObjectListInterface
    {
        /* @var DomainEntityInterface $other_entity */
        /* @var DomainEntityInterface entity */
        $different_entities = [];
        foreach ($this->internal_vector as $pos => $entity) {
            if (!$other_list->has($pos)) {
                $different_entities[] = $entity;
                continue;
            }
            $other_entity = $other_list->get($pos);
            $diff = $entity->getValueObjectMap()->diff($other_entity->getValueObjectMap());
            if (!$diff->isEmpty()) {
                $different_entities[] = $entity;
            }
        }
        return new static($different_entities);
    }
}
