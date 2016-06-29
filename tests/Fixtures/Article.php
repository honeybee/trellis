<?php

namespace Trellis\Tests\Fixtures;

use Trellis\Entity\Entity;

class Article extends Entity
{
    public function getIdentifier()
    {
        return $this->getValue('uuid');
    }

    public function getTitle()
    {
        return $this->getValue('title');
    }
}
