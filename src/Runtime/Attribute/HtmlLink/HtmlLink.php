<?php

namespace Trellis\Runtime\Attribute\HtmlLink;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\ValueHolder\ComplexValue;

class HtmlLink extends ComplexValue
{
    const PROPERTY_HREF     = 'href';
    const PROPERTY_TEXT     = 'text';
    const PROPERTY_TITLE    = 'title';
    const PROPERTY_HREFLANG = 'hreflang';
    const PROPERTY_REL      = 'rel';
    const PROPERTY_TARGET   = 'target';
    const PROPERTY_DOWNLOAD = 'download';

    protected $values = [
        self::PROPERTY_HREF     => '',
        self::PROPERTY_TEXT     => '',
        self::PROPERTY_TITLE    => '',
        self::PROPERTY_HREFLANG => '',
        self::PROPERTY_REL      => '',
        self::PROPERTY_TARGET   => '',
        self::PROPERTY_DOWNLOAD => false,
    ];

    public static function getMandatoryPropertyNames()
    {
        return [];
    }

    public static function getPropertyMap()
    {
        return [
            self::PROPERTY_HREF     => self::VALUE_TYPE_URL,
            self::PROPERTY_TITLE    => self::VALUE_TYPE_TEXT,
            self::PROPERTY_TEXT     => self::VALUE_TYPE_TEXT,
            self::PROPERTY_HREFLANG => self::VALUE_TYPE_TEXT,
            self::PROPERTY_REL      => self::VALUE_TYPE_TEXT,
            self::PROPERTY_TARGET   => self::VALUE_TYPE_TEXT,
            self::PROPERTY_DOWNLOAD => self::VALUE_TYPE_BOOLEAN,
        ];
    }

    public function hasHref()
    {
        return !empty($this->values[self::PROPERTY_HREF]);
    }

    public function getHref()
    {
        return $this->values[self::PROPERTY_HREF];
    }

    public function getText()
    {
        return $this->values[self::PROPERTY_TEXT];
    }

    public function getTitle()
    {
        return $this->values[self::PROPERTY_TITLE];
    }

    public function getHreflang()
    {
        return $this->values[self::PROPERTY_HREFLANG];
    }

    public function getRel()
    {
        return $this->values[self::PROPERTY_REL];
    }

    public function getTarget()
    {
        return $this->values[self::PROPERTY_TARGET];
    }

    public function getDownload()
    {
        return $this->values[self::PROPERTY_DOWNLOAD];
    }

    public function __toString()
    {
        return $this->values[self::PROPERTY_HREF];
    }
}
