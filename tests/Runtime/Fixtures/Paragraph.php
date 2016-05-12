<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Runtime\Entity\Entity;

class Paragraph extends Entity
{
    public function getIdentifier()
    {
        return $this->getTitle();
    }

    public function getTitle()
    {
        return $this->getValue('title');
    }

    public function getContent()
    {
        return $this->getValue('content');
    }
}
