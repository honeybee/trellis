<?php

namespace Trellis\Entity\ValueObject;

use Assert\Assertion;
use Trellis\Entity\ValueObjectInterface;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid implements ValueObjectInterface
{
    const EMPTY = null;

    /**
     * @var RamseyUuid $uuid
     */
    private $uuid;

    /**
     * @return Uuid
     */
    public static function generate(): Uuid
    {
        return new Uuid(RamseyUuid::uuid4()->toString());
    }

    /**
     * Uuid constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid = self::EMPTY)
    {
        if ($uuid !== self::EMPTY) {
            Assertion::uuid($uuid);
            $this->uuid = RamseyUuid::fromString($uuid);
        } else {
            $this->uuid = $uuid;
        }
    }

    /**
     * @param ValueObjectInterface $other_value
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->uuid === self::EMPTY;
    }

    /**
     * @return null|string
     */
    public function toNative(): ?string
    {
        return $this->isEmpty() ? $this->uuid : $this->uuid->toString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->isEmpty() ? "null" : $this->uuid->toString();
    }
}
