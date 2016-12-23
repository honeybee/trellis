<?php

namespace Trellis\Tests\Fixture;

use Trellis\DomainEntityInterface;
use Trellis\Entity\DomainEntityTrait;
use Trellis\Entity\DomainValueObjectTrait;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Text;
use Trellis\Entity\ValueObject\Integer;

class Paragraph implements DomainEntityInterface, ValueObjectInterface
{
    use DomainEntityTrait;
    use DomainValueObjectTrait;

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
        return $this->get('id');
    }

    /**
     * @return Text
     */
    public function getKicker(): Text
    {
        return $this->get('kicker');
    }

    /**
     * @return Text
     */
    public function getContent(): Text
    {
        return $this->get('content');
    }
}
