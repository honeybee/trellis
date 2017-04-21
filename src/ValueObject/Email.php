<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class Email implements ValueObjectInterface
{
    /**
     * @var string
     */
    private const NIL = "";

    /**
     * @var Text
     */
    private $localPart;

    /**
     * @var Text
     */
    private $domain;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue, array $context = [])
    {
        return $nativeValue ? new static($nativeValue) : self::makeEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(self::NIL);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        Assertion::isInstanceOf($otherValue, Email::class);
        return $this->toNative() === $otherValue->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->localPart->isEmpty() || $this->domain->isEmpty();
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->isEmpty() ? self::NIL : $this->localPart->toNative()."@".$this->domain->toNative();
    }

    /**
     * @return Text
     */
    public function getLocalPart(): Text
    {
        return $this->localPart;
    }

    /**
     * @return Text
     */
    public function getDomain(): Text
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toNative();
    }

    /**
     * @param string $email
     */
    private function __construct(string $email)
    {
        if ($email !== self::NIL) {
            Assertion::email($email, "Trying to create email from invalid string.");
            $parts = explode("@", $email);
            $this->localPart = Text::fromNative($parts[0]);
            $this->domain = Text::fromNative(trim($parts[1], "[]"));
        } else {
            $this->localPart = Text::makeEmpty();
            $this->domain = Text::makeEmpty();
        }
    }
}
