<?php

namespace Trellis\EntityType\Attribute\Email;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Email implements ValueInterface
{
    use NativeEqualsComparison;

    const NIL = '';

    /**
     * @var string $email
     */
    private $email;

    /**
     * @param string $email
     */
    public function __construct($email = self::NIL)
    {
        if ($email !== '') {
            Assertion::email($email, 'Email format is invalid.');
        }

        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->email === self::NIL;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->email;
    }
}
