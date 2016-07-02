<?php

namespace Trellis\Tests\Fixtures;

use Trellis\Entity\Entity;

class Paragraph extends Entity
{
    public function getIdentifier()
    {
        return $this->get('uuid');
    }

    public function getUuid()
    {
        return $this->get('uuid');
    }

    public function getKicker()
    {
        return $this->get('kicker');
    }

    public function getContent()
    {
        return $this->get('content');
    }
}
