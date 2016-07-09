<?php

namespace Trellis\EntityType\Attribute\Token;

use Trellis\EntityType\Attribute\Text\Text;

class Token extends Text
{
    public static function generate($max_length = 40)
    {
        return new static(bin2hex(mcrypt_create_iv(ceil($max_length / 2), MCRYPT_DEV_URANDOM)));
    }
}
