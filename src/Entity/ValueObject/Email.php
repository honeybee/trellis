<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Assert\Assertion;

final class Email implements ValueObjectInterface
{
    /**
     * @var Text $local_part;
     */
    private $local_part;

    /**
     * @var Text $domain;
     */
    private $domain;

    /**
     * @param string $email
     */
    public function __construct(string $email = Text::EMPTY)
    {
        if ($email !== Text::EMPTY) {
            Assertion::email($email, 'Trying to create email from invalid string.');
            $parts = explode('@', $email);
            $this->local_part = new Text($parts[0]);
            $this->domain = new Text(trim($parts[1], '[]'));
        } else {
            $this->local_part = new Text;
            $this->domain = new Text;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, Email::CLASS);
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->local_part->isEmpty() || $this->domain->isEmpty();
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->isEmpty() ? Text::EMPTY : $this->local_part->toNative().'@'.$this->domain->toNative();
    }

    /**
     * @return Text
     */
    public function getLocalPart(): Text
    {
        return $this->local_part;
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
}
