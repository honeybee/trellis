<?php

namespace Trellis\EntityType\Attribute\Email;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Text\Text;

class Email extends Text
{
    /**
     * @param string $email
     */
    public function __construct($email = self::NIL)
    {
        if ($email !== self::NIL) {
            Assertion::email($email, 'Email format is invalid.');
        }

        parent::__construct($email);
    }
}
