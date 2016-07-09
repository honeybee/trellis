<?php

namespace Trellis\EntityType\Attribute\Token;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Token implements ValueInterface
{
    use NativeEqualsComparison;

    const NIL = '';

    /**
     * @var string $token
     */
    private $token;

    public static function generate($max_length = 40)
    {
        return new static(bin2hex(mcrypt_create_iv(ceil($max_length / 2), MCRYPT_DEV_URANDOM)));
    }

    /**
     * @param string $token
     */
    public function __construct($token = self::NIL)
    {
        Assertion::string($token, 'Token may only be constructed from string.');

        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->token === self::NIL;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->token;
    }
}
