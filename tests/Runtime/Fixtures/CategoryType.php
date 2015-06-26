<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Common\Options;
use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Attribute\Textarea\TextareaAttribute;
use Trellis\Runtime\EntityType;

class CategoryType extends EntityType
{
    public function __construct()
    {
        parent::__construct(
            'Category',
            [
                new TextAttribute('title', $this),
                new TextareaAttribute('description', $this)
            ],
            new Options(
                [
                    'foo' => 'bar',
                    'nested' => [
                        'foo' => 'bar',
                        'blah' => 'blub'
                    ]
                ]
            )
        );
    }

    public static function getEntityImplementor()
    {
        return Category::CLASS;
    }
}
