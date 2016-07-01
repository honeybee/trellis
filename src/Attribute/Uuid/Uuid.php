<?php

namespace Trellis\Attribute\Uuid;

use Assert\Assertion;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Trellis\Value\ValueInterface;

class Uuid implements ValueInterface
{
    private $uuid;

    public function __construct($uuid = '')
    {
        Assertion::string($uuid, 'Uuid may only be constructed from string.');

        if (!empty($uuid)) {
            $this->uuid = RamseyUuid::fromString($uuid);
        }
    }

    public function isEqualTo(ValueInterface $other_value)
    {
        return $this->toNative() === $other_value->toNative();
    }

    public function isEmpty()
    {
        return empty($this->uuid);
    }

    public function toNative()
    {
        return $this->uuid;
    }

    public static function generate()
    {
        return RamseyUuid::uuid4()->toString();
    }
}
