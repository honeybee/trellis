<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Runtime\Entity\Entity;

class Paragraph extends Entity
{
    public function getIdentifier()
    {
        return $this->getValue('title');
    }
}
