<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class Timestamp implements ValueObjectInterface
{
    /**
     * @var string
     */
    public const NATIVE_FORMAT = "Y-m-d\\TH:i:s.uP";

    /**
     * @var null
     */
    private const NIL = null;

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var string
     */
    private $originalFormat;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue, array $context = [])
    {
        return $nativeValue ? self::createFromString($nativeValue) : self::makeEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(self::NIL);
    }

    /**
     * @return Timestamp
     */
    public static function now(): Timestamp
    {
        return new Timestamp(new \DateTimeImmutable);
    }

    /**
     * @param string $date
     * @param string $format
     * @return Timestamp
     */
    public static function createFromString(string $date, string $format = self::NATIVE_FORMAT): Timestamp
    {
        Assertion::date($date, $format);
        return new Timestamp(\DateTimeImmutable::createFromFormat($format, $date), $format);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        Assertion::isInstanceOf($otherValue, Timestamp::class);
        return $this->toNative() === $otherValue->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->timestamp === self::NIL;
    }

    /**
     * @return null|string
     */
    public function toNative(): ?string
    {
        return !$this->isEmpty() ? $this->timestamp->format(self::NATIVE_FORMAT) : self::NIL;
    }

    /**
     * @return string
     */
    public function getOriginalFormat(): string
    {
        return $this->originalFormat;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->isEmpty() ? "null" : $this->toNative();
    }

    /**
     * @param \DateTimeImmutable|null $timestamp
     * @param string $format
     */
    private function __construct(\DateTimeImmutable $timestamp = null, string $format = self::NATIVE_FORMAT)
    {
        $this->timestamp = $timestamp ? $timestamp->setTimezone(new \DateTimeZone("UTC")) : $timestamp;
        $this->originalFormat = $format;
    }
}
