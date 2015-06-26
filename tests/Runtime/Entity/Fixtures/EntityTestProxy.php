<?php

namespace Trellis\Tests\Runtime\Entity\Fixtures;

use Trellis\Runtime\Entity\Entity;

class EntityTestProxy extends Entity
{
    public function getIdentifier()
    {
        return 'some-identifier';
    }
}
