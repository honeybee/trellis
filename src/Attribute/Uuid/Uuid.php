<?php

namespace Trellis\Attribute\Uuid;

use Assert\Assertion;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Trellis\Attribute\AttributeInterface;
use Trellis\Value\HasAttribute;
use Trellis\Value\ValueInterface;

class Uuid implements ValueInterface
{
    use HasAttribute;

    private $uuid;

    public function __construct(AttributeInterface $attribute, $uuid = '')
    {
        Assertion::string($uuid, 'Uuid may only be constructed from string.');

        $this->attribute = $attribute;
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
