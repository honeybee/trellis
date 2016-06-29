<?php

namespace Trellis\Value;

use Assert\Assertion;
use Ramsey\Uuid\Uuid;

class Uuid implements ValueInterface
{
    private $uuid;

    public function __construct($uuid_string = null)
    {
        Assertion::string($text, 'Uuid may only be constructed from string.');

        $this->uuid = Uuid::fromString($uuid_string ?: Uuid::uuid4());
    }

    public function isEqualTo(ValueInterface $other_value)
    {
        return $this->toNative() === $other_value->toNative();
    }

    public function toNative()
    {
        return $this->uuid->toString();
    }
}
