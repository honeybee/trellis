<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Runtime\Entity\Entity;

class Article extends Entity
{
    public function getIdentifier()
    {
        return $this->getValue('uuid');
    }

    public function getContentObjects()
    {
        return $this->getValue('content_objects');
    }

    public function getContent()
    {
        return $this->getValue('content');
    }
}
