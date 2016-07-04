<?php

namespace Trellis\Attribute\Uuid;

use Assert\Assertion;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Trellis\Value\NativeEqualsComparison;
use Trellis\Value\ValueInterface;

class Uuid implements ValueInterface
{
    use NativeEqualsComparison;

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
