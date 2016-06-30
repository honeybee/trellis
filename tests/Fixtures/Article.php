<?php

namespace Trellis\Tests\Fixtures;

use Trellis\Entity\Entity;

class Article extends Entity
{
    public function getIdentifier()
    {
        return $this->get('uuid');
    }

    public function getTitle()
    {
        return $this->get('title');
    }
}
