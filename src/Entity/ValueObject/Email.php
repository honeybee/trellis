<?php

namespace Trellis\Entity\ValueObject;

use Assert\Assertion;
use Trellis\Entity\ValueObjectEqualsTrait;
use Trellis\Entity\ValueObjectInterface;

final class Email implements ValueObjectInterface
{
    use ValueObjectEqualsTrait;

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
