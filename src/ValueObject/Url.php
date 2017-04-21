<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class Url implements ValueObjectInterface
{
    /**
     * @var string
     */
    private const NIL = "";

    /**
     * @var string
     */
    private const DEFAULT_PATH = "/";

    /**
     * @var Text
     */
    private $fragment;

    /**
     * @var Text
     */
    private $host;

    /*
     * @var Text
     */
    private $scheme;

    /**
     * @var Text
     */
    private $query;

    /**
     * @var Integer
     */
    private $port;

    /**
     * @var Text
     */
    private $path;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue, array $context = [])
    {
        return $nativeValue ? new static($nativeValue) : self::makeEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(self::NIL);
    }

    /**
     * @param ValueObjectInterface $otherValue
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        /* @var Url $otherValue */
        Assertion::isInstanceOf($otherValue, Url::class);
        return $this->host->equals($otherValue->getHost())
            && $this->scheme->equals($otherValue->getScheme())
            && $this->port->equals($otherValue->getPort())
            && $this->query->equals($otherValue->getQuery())
            && $this->fragment->equals($otherValue->getFragment())
            && $this->path->equals($otherValue->getPath());
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->host->isEmpty()
            || $this->scheme->isEmpty()
            || $this->path->isEmpty();
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        if ($this->isEmpty()) {
            return self::NIL;
        }
        return sprintf(
            "%s://%s%s%s%s%s",
            $this->scheme,
            $this->host,
            $this->port->isEmpty() ? "" : ":".$this->port,
            $this->path,
            $this->query->isEmpty() ? "" : "?".$this->query,
            $this->fragment->isEmpty() ? "" : "#".$this->fragment
        );
    }

    /**
     * @return Text
     */
    public function getPath(): Text
    {
        return $this->path;
    }

    /**
     * @return Integer
     */
    public function getPort(): Integer
    {
        return $this->port;
    }

    /**
     * @return Text
     */
    public function getFragment(): Text
    {
        return $this->fragment;
    }

    /**
     * @return Text
     */
    public function getHost(): Text
    {
        return $this->host;
    }

    /**
     * @return Text
     */
    public function getQuery(): Text
    {
        return $this->query;
    }

    /**
     * @return Text
     */
    public function getScheme(): Text
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->isEmpty() ? self::NIL : $this->toNative();
    }

    /**
     * @param string $url
     */
    private function __construct(string $url)
    {
        if ($url !== self::NIL) {
            Assertion::url($url);
            $this->host = Text::fromNative(parse_url($url, PHP_URL_HOST));
            $this->scheme = Text::fromNative(parse_url($url, PHP_URL_SCHEME));
            $this->query = Text::fromNative(parse_url($url, PHP_URL_QUERY) ?? "");
            $this->port = Integer::fromNative(parse_url($url, PHP_URL_PORT));
            $this->fragment = Text::fromNative(parse_url($url, PHP_URL_FRAGMENT) ?? "");
            $this->path = Text::fromNative(parse_url($url, PHP_URL_PATH) ?? self::DEFAULT_PATH);
        } else {
            $this->host = Text::makeEmpty();
            $this->scheme = Text::makeEmpty();
            $this->query = Text::makeEmpty();
            $this->port = Integer::makeEmpty();
            $this->fragment = Text::makeEmpty();
            $this->path = Text::makeEmpty();
        }
    }
}
