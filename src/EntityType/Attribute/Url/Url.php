<?php

namespace Trellis\EntityType\Attribute\Url;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Url implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var string $url
     */
    private $url;

    /**
     * @param string $url
     */
    public function __construct($url = '')
    {
        if ($url !== '') {
            Assertion::url($url, 'Url format is invalid.');
        }

        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->url);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->url;
    }
}
