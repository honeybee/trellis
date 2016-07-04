<?php

namespace Trellis\EntityType\Attribute\Uuid;

use Assert\Assertion;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Uuid implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var string $uuid
     */
    private $uuid;

    /**
     * Creates a new Uuid instance holding a randomly generated uuid version 4.
     *
     * @return Uuid
     */
    public static function generate()
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    /**
     * @param string $uuid
     */
    public function __construct($uuid = '')
    {
        Assertion::string($uuid, 'Uuid may only be constructed from string.');

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
