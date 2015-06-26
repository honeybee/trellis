<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Runtime\EntityType;

class InvalidType extends EntityType
{
    public function __construct()
    {
        parent::__construct('InvalidType');
    }

    public static function getEntityImplementor()
    {
        return 'NonExistantEntityClass';
    }
}
