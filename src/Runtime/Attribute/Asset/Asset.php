<?php

namespace Trellis\Runtime\Attribute\Asset;

use Trellis\Runtime\ValueHolder\ComplexValue;

class Asset extends ComplexValue
{
    const PROPERTY_LOCATION = 'location';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CAPTION = 'caption';
    const PROPERTY_COPYRIGHT = 'copyright';
    const PROPERTY_COPYRIGHT_URL = 'copyright_url';
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_META_DATA = 'meta_data';

    protected $values = [
        self::PROPERTY_LOCATION => '',
        self::PROPERTY_TITLE => '',
        self::PROPERTY_CAPTION => '',
        self::PROPERTY_COPYRIGHT => '',
        self::PROPERTY_COPYRIGHT_URL => '',
        self::PROPERTY_SOURCE => '',
        self::PROPERTY_META_DATA => []
    ];

    public function getMandatoryPropertyNames()
    {
        return [
            self::PROPERTY_LOCATION
        ];
    }

    public function __toString()
    {
        return $this->location;
    }
}
