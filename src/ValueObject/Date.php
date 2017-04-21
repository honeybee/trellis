<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class Date implements ValueObjectInterface
{
    /**
     * @var string
     */
    public const NATIVE_FORMAT = "Y-m-d";

    /**
     * @var null
     */
    private const NIL = null;

    /**
     * @var string
     */
    private $dateVal;

    /**
     * @var string
     */
    private $originalFormat;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue): ValueObjectInterface
    {
        return $nativeValue !== self::NIL
            ? self::createFromString($nativeValue)
            : self::makeEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static();
    }

    /**
     * @return Date
     */
    public static function today(): Date
    {
        return new static(new \DateTimeImmutable);
    }

    /**
     * @param string $dateVal
     * @param string $format
     * @return Date
     */
    public static function createFromString(string $dateVal, string $format = self::NATIVE_FORMAT): Date
    {
        Assertion::date($dateVal, $format);
        return new Date((\DateTimeImmutable::createFromFormat($format, $dateVal)), $format);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        Assertion::isInstanceOf($otherValue, Date::class);
        return $this->toNative() === $otherValue->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->dateVal === self::NIL;
    }

    /**
     * @return null|string
     */
    public function toNative(): ?string
    {
        return !$this->isEmpty() ? $this->dateVal->format(self::NATIVE_FORMAT) : self::NIL;
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
        return !$this->isEmpty() ? $this->dateVal->format($this->originalFormat) : "";
    }

    /**
     * @param \DateTimeImmutable|null $dateVal
     * @param string $originalFormat
     */
    private function __construct(\DateTimeImmutable $dateVal = self::NIL, string $originalFormat = self::NATIVE_FORMAT)
    {
        $this->dateVal = $dateVal ? $dateVal->setTime(0, 0, 0) : $dateVal;
        $this->originalFormat = $originalFormat;
    }
}
