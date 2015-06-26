<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Runtime\Entity\Entity;

class Article extends Entity
{
    public function getIdentifier()
    {
        return $this->getValue('uuid');
    }
}
