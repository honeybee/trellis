<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Common\Options;
use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\EntityType;
use Trellis\Runtime\EntityTypeInterface;
use Trellis\Runtime\Attribute\AttributeInterface;

class WorkflowStateType extends EntityType
{
    public function __construct(EntityTypeInterface $parent, AttributeInterface $parent_attribute)
    {
        parent::__construct(
            'WorkflowState',
            [
                new TextAttribute('workflow_name', $this, [], $parent_attribute),
                new TextAttribute('workflow_step', $this, [], $parent_attribute)
            ],
            new Options(
                [
                    'foo' => 'bar',
                    'nested' => [
                        'foo' => 'bar',
                        'blah' => 'blub'
                    ]
                ]
            ),
            $parent,
            $parent_attribute
        );
    }

    /**
     * Returns the EntityInterface implementor to use when creating new documents.
     *
     * @return string Fully qualified name of an EntityInterface implementation.
     */
    public static function getEntityImplementor()
    {
        return WorkflowState::CLASS;
    }
}
