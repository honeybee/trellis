<?php

namespace Trellis\Entity\ValueObject;

use Assert\Assertion;
use Trellis\Entity\ValueObjectInterface;

final class Email implements ValueObjectInterface
{
    /**
     * @var Text $email;
     */
    private $email;

    /**
     * @param string $email
     */
    public function __construct(string $email = Text::EMPTY)
    {
        if ($email !== Text::EMPTY) {
            Assertion::email($email, 'Trying to create email from invalid string.');
        }
        $this->email = new Text($email);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, Email::CLASS);
        return $this->email->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->email->isEmpty();
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->email->toNative();
    }
}
