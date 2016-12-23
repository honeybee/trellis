<?php

namespace Trellis\Tests\Fixture;

use Trellis\Entity\Entity;
use Trellis\Entity\ValueObject\EntityList;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Integer;
use Trellis\Entity\ValueObject\Text;

final class Article extends Entity
{
    /**
     * @return ValueObjectInterface
     */
    public function getIdentity(): ValueObjectInterface
    {
        return $this->getId();
    }

    /**
     * @return int
     */
    public function getId(): Integer
    {
        return $this->get('id');
    }

    /**
     * @return Text
     */
    public function getTitle(): Text
    {
        return $this->get('title');
    }

    /**
     * @return EntityList
     */
    public function getContentObjects(): EntityList
    {
        return $this->get('content_objects');
    }
}
