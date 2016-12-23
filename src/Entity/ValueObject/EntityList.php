<?php

namespace Trellis\Entity\ValueObject;

use Ds\Vector;
use Trellis\DomainEntityInterface;
use Trellis\Entity\ValueObjectListInterface;
use Trellis\Entity\ValueObjectListTrait;

final class EntityList implements ValueObjectListInterface
{
    use ValueObjectListTrait {
        ValueObjectListTrait::__construct as private __init;
    }

    /**
     * @var Vector $internal_vector
     */
    private $internal_vector;

    /**
     * @param iterable|null|DomainEntityInterface[] $entities
     */
    public function __construct(iterable $entities = null)
    {
        (function (DomainEntityInterface ...$entities): void {
            $this->__init($entities);
        })(...$entities ?? []);
    }

    /**
     * @param ValueObjectListInterface $other_list
     *
     * @return ValueObjectListInterface
     */
    public function diff(ValueObjectListInterface $other_list): ValueObjectListInterface
    {
        $different_entities = [];
        foreach ($this->internal_vector as $pos => $entity) {
            if (!$other_list->has($pos)) {
                $different_entities[] = $entity;
                continue;
            }
            $diff = $entity->getValueObjectMap()->diff($other_list->get($pos)->getValueObjectMap());
            if (!$diff->isEmpty()) {
                $different_entities[] = $entity;
            }
        }
        return new static($different_entities);
    }
}
