<?php

namespace Trellis\Tests\Fixture;

use Trellis\Entity\Entity;
use Trellis\Entity\ValueObject\NestedEntityList;
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
        return $this->get("id");
    }

    /**
     * @return Text
     */
    public function getTitle(): Text
    {
        return $this->get("title");
    }

    /**
     * @return NestedEntityList
     */
    public function getParagraphs(): NestedEntityList
    {
        return $this->get("paragraphs");
    }

    /**
     * @return ValueObjectInterface (NestedEntity|Nil)
     */
    public function getCategories(): ValueObjectInterface
    {
        return $this->get("categories");
    }
}
