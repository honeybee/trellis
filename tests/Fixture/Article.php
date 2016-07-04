<?php

namespace Trellis\Tests\Fixture;

use Trellis\Entity\Entity;

class Article extends Entity
{
    public function getIdentifier()
    {
        return $this->get('uuid');
    }

    public function getUuid()
    {
        return $this->get('uuid');
    }

    public function getTitle()
    {
        return $this->get('title');
    }

    public function getContentObjects()
    {
        return $this->get('content_objects');
    }
}
