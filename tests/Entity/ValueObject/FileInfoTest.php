<?php

namespace Trellis\Tests\Entity\ValueObject;

use Trellis\Entity\ValueObject\FileInfo;
use Trellis\Entity\ValueObject\Decimal;
use Trellis\Entity\ValueObject\Text;
use Trellis\Entity\ValueObject\Url;
use Trellis\Tests\TestCase;

final class FileInfoTest extends TestCase
{
    private const FIXED_DATA = [
        "path" => "foo/bar.jpg",
        "file_name" => "bar.jpg",
        "file_size" => 12.34,
        "mime_type" => "image/jpeg",
        "title" => "some title",
        "caption" => "some caption",
        "copyright" => "some copyright info/disclaimer",
        "copyright_url" => "http://www.example.com/foo/bar.jpg",
    ];

    private $file_info;

    public function testToNative(): void
    {
        $this->assertEquals(self::FIXED_DATA, $this->file_info->toNative());
        $this->assertNull((new FileInfo)->toNative());
    }

    public function testEquals(): void
    {
        $this->assertTrue($this->file_info->equals(FileInfo::fromNative(self::FIXED_DATA)));
        $this->assertFalse($this->file_info->equals(new FileInfo));
    }

    public function testToString(): void
    {
        $this->assertEquals(self::FIXED_DATA["path"], (string)$this->file_info);
    }

    protected function setUp(): void
    {
        $this->file_info = new FileInfo(
            new Text(self::FIXED_DATA["path"]),
            new Text(self::FIXED_DATA["file_name"]),
            new Decimal(self::FIXED_DATA["file_size"]),
            new Text(self::FIXED_DATA["mime_type"]),
            new Text(self::FIXED_DATA["title"]),
            new Text(self::FIXED_DATA["caption"]),
            new Text(self::FIXED_DATA["copyright"]),
            new Url(self::FIXED_DATA["copyright_url"])
        );
    }
}
