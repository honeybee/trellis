<?php

namespace Trellis\Tests\Fixture;

use Trellis\DomainEntityInterface;
use Trellis\Entity\DomainEntityTrait;
use Trellis\Entity\DomainValueObjectTrait;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObject\Integer;
use Trellis\Entity\ValueObject\Text;

final class Article implements DomainEntityInterface, ValueObjectInterface
{
    use DomainEntityTrait;
    use DomainValueObjectTrait;

    public function getIdentity(): ValueObjectInterface
    {
        return $this->getId();
    }

    public function getId(): Integer
    {
        return $this->get('id');
    }

    public function getTitle(): Text
    {
        return $this->get('title');
    }

    public function getContentObjects()
    {
        return $this->get('content_objects');
    }
}
