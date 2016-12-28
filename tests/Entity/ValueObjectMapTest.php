<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObjectMap;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;

final class ValueObjectMapTest extends TestCase
{
    private const FIXED_DATA = [
        "id" => 42,
        "title" => "hello world!"
    ];

    /**
     * @var ValueObjectMap $value_object_map
     */
    private $value_object_map;

    public function testCount(): void
    {
        $this->assertCount(3, $this->value_object_map);
    }

    protected function setUp(): void
    {
        $this->value_object_map = (new ArticleType)
            ->makeEntity(self::FIXED_DATA)
            ->getValueObjectMap();
    }
}
