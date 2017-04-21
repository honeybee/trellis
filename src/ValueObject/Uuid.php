<?php

namespace Trellis\ValueObject;

use Assert\Assertion;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid implements ValueObjectInterface
{
    /**
     * @var null
     */
    private const NIL = null;

    /**
     * @var RamseyUuid
     */
    private $uuid;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue): ValueObjectInterface
    {
        return $nativeValue ? new static(RamseyUuid::fromString($nativeValue)) : self::makeEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(self::NIL);
    }

    /**
     * @return Uuid
     */
    public static function generate(): Uuid
    {
        return new static(RamseyUuid::uuid4());
    }

    /**
     * @param ValueObjectInterface $otherValue
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        return $this->toNative() === $otherValue->toNative();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->uuid === self::NIL;
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

    /**
     * @param RamseyUuid|null $uuid
     */
    private function __construct(RamseyUuid $uuid = null)
    {
        $this->uuid = $uuid;
    }
}
