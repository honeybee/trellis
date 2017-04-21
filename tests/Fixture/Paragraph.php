<?php

namespace Trellis\Tests\Fixture;

use Trellis\Entity\NestedEntity;
use Trellis\ValueObject\Integer;
use Trellis\ValueObject\Text;
use Trellis\ValueObject\ValueObjectInterface;

final class Paragraph extends NestedEntity
{
    /**
     * @return ValueObjectInterface
     */
    public function getIdentity(): ValueObjectInterface
    {
        return $this->getId();
    }

    /**
     * @return Integer
     */
    public function getId(): Integer
    {
        return $this->get("id");
    }

    /**
     * @return Text
     */
    public function getKicker(): Text
    {
        return $this->get("kicker");
    }

    /**
     * @return Text
     */
    public function getContent(): Text
    {
        return $this->get("content");
    }
}
