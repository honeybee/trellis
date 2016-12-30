<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Assert\Assertion;
use Trellis\Entity\ValueObjectInterface;

final class FileInfo implements ValueObjectInterface
{
    public const EMPTY = null;

    /**
     * @var Text $path
     */
    private $path;

    /**
     * @var Text $file_name
     */
    private $file_name;

    /**
     * @var Decimal $file_size
     */
    private $file_size;

    /**
     * @var Text $mime_type
     */
    private $mime_type;

    /**
     * @var Text $title
     */
    private $title;

    /**
     * @var Text $caption
     */
    private $caption;

    /**
     * @var Text $copyright
     */
    private $copyright;

    /**
     * @var Url $copyright_url
     */
    private $copyright_url;

    /**
     * @param array $asset_data
     *
     * @return FileInfo
     */
    public static function fromNative(array $asset_data): FileInfo
    {
        return new FileInfo(
            new Text($asset_data["path"] ?? Text::EMPTY),
            new Text($asset_data["file_name"] ?? Text::EMPTY),
            new Decimal($asset_data["file_size"] ?? Decimal::EMPTY),
            new Text($asset_data["mime_type"] ?? Text::EMPTY),
            new Text($asset_data["title"] ?? Text::EMPTY),
            new Text($asset_data["caption"] ?? Text::EMPTY),
            new Text($asset_data["copyright"] ?? Text::EMPTY),
            new Url($asset_data["copyright_url"] ?? Url::EMPTY)
        );
    }

    /**
     * @param Text|null $path
     * @param Text|null $file_name
     * @param Decimal|null $file_size
     * @param Text|null $mime_type
     * @param Text|null $title
     * @param Text|null $caption
     * @param Text|null $copyright
     * @param Url|null $copyright_url
     */
    public function __construct(
        Text $path = null,
        Text $file_name = null,
        Decimal $file_size = null,
        Text $mime_type = null,
        Text $title = null,
        Text $caption = null,
        Text $copyright = null,
        Url $copyright_url = null
    ) {
        $this->path = $path ?? new Text;
        if (!$this->path->isEmpty()) {
            $this->file_name = $file_name ?? new Text;
            $this->file_size = $file_size ?? new Decimal;
            $this->mime_type = $mime_type ?? new Text;
            $this->title = $title ?? new Text;
            $this->caption = $caption ?? new Text;
            $this->copyright = $copyright ?? new Text;
            $this->copyright_url = $copyright_url ?? new Url;
        }
    }

    /**
     * @param ValueObjectInterface $other_value
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        /* @var FileInfo $other_value */
        Assertion::isInstanceOf($other_value, FileInfo::CLASS);
        return $this->path->equals($other_value->getPath())
            && $this->file_name->equals($other_value->getFileName())
            && $this->file_size->equals($other_value->getFileSize())
            && $this->mime_type->equals($other_value->getMimeType())
            && $this->title->equals($other_value->getTitle())
            && $this->caption->equals($other_value->getCaption())
            && $this->copyright->equals($other_value->getCopyright())
            && $this->copyright_url->equals($other_value->getCopyrightUrl());
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->path->isEmpty();
    }

    /**
     * @return mixed
     */
    public function toNative()
    {
        if ($this->isEmpty()) {
            return self::EMPTY;
        }
        return [
            "path" => $this->path->toNative(),
            "file_name" => $this->file_name->toNative(),
            "file_size" => $this->file_size->toNative(),
            "mime_type" => $this->mime_type->toNative(),
            "title" => $this->title->toNative(),
            "caption" => $this->caption->toNative(),
            "copyright" => $this->copyright->toNative(),
            "copyright_url" => $this->copyright_url->toNative()
        ];
    }

    /**
     * @return Text
     */
    public function getPath(): Text
    {
        return $this->path;
    }

    /**
     * @return Text
     */
    public function getFileName(): Text
    {
        return $this->file_name;
    }

    /**
     * @return Decimal
     */
    public function getFileSize(): Decimal
    {
        return $this->file_size;
    }

    /**
     * @return Text
     */
    public function getMimeType(): Text
    {
        return $this->mime_type;
    }

    /**
     * @return Text
     */
    public function getTitle(): Text
    {
        return $this->title;
    }

    /**
     * @return Text
     */
    public function getCaption(): Text
    {
        return $this->caption;
    }

    /**
     * @return Text
     */
    public function getCopyright(): Text
    {
        return $this->copyright;
    }

    /**
     * @return Url
     */
    public function getCopyrightUrl(): Url
    {
        return $this->copyright_url;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->path;
    }
}
