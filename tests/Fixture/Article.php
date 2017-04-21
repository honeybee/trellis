<?php

namespace Trellis\Tests\Fixture;

use Trellis\Entity\Entity;
use Trellis\Entity\NestedEntityList;
use Trellis\ValueObject\Integer;
use Trellis\ValueObject\Text;
use Trellis\ValueObject\Url;
use Trellis\ValueObject\Uuid;
use Trellis\ValueObject\ValueObjectInterface;

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
     * @return Uuid
     */
    public function getId(): Uuid
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
     * @return Url
     */
    public function getUrl(): Url
    {
        return $this->get("url");
    }

    /**
     * @return Paragraph
     */
    public function getParagraphs(): NestedEntityList
    {
        return $this->get("paragraphs");
    }

    /**
     * @return Location
     */
    public function getWorkshopLocation(): Location
    {
        return $this->get("workshop_location");
    }
}
