<?php

namespace Trellis\EntityType\Attribute\Url;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Text\Text;

class Url extends Text
{
    /**
     * @param string $url
     */
    public function __construct($url = self::NIL)
    {
        if ($url !== self::NIL) {
            Assertion::url($url, 'Url format is invalid.');
        }

        parent::__construct($url);
    }
}
