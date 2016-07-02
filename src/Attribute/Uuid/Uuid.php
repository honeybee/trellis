<?php

namespace Trellis\Attribute\Uuid;

use Assert\Assertion;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Trellis\Attribute\AttributeInterface;
use Trellis\Value\CanEqualNativeValue;
use Trellis\Value\HasAttribute;
use Trellis\Value\ValueInterface;

class Uuid implements ValueInterface
{
    use HasAttribute;
    use CanEqualNativeValue;

    /**
     * @var string $uuid
     */
    private $uuid;

    /**
     * @return string A new uuid version 4 string.
     */
    public static function generate()
    {
        return RamseyUuid::uuid4()->toString();
    }

    /**
     * @param AttributeInterface $attribute
     * @param string $uuid
     */
    public function __construct(AttributeInterface $attribute, $uuid = '')
    {
        Assertion::string($uuid, 'Uuid may only be constructed from string.');

        $this->attribute = $attribute;
        if (!empty($uuid)) {
            $this->uuid = RamseyUuid::fromString($uuid);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->uuid);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->uuid;
    }
}
