<?php

namespace Trellis\EntityType;

use Trellis\Assert\Assert;
use Trellis\Assert\Assertion;
use Trellis\Entity\EntityInterface;
use Trellis\Entity\NestedEntityList;
use Trellis\Entity\TypedEntityInterface;
use Trellis\ValueObject\ValueObjectInterface;

class NestedEntityListAttribute extends NestedEntityAttribute
{
    /**
     * {@inheritdoc}
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        if ($value instanceof NestedEntityList) {
            foreach ($value as $entity) {
                parent::makeValue($entity); // will check for type compliance
            }
            return $value;
        }
        Assert::that($value)->nullOr()->isArray();
        return is_null($value) ? new NestedEntityList : $this->makeEntityList($value, $parent);
    }

    /**
     * @param array $values
     * @param TypedEntityInterface $parentEntity
     * @return Vector
     */
    private function makeEntityList(array $values, TypedEntityInterface $parentEntity = null): NestedEntityList
    {
        return new NestedEntityList(
            array_map(function (array $entityValues) use ($parentEntity) {
                return parent::makeValue($entityValues, $parentEntity);
            }, $values)
        );
    }
}
