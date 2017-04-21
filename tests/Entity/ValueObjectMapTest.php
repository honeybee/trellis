<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObjectMap;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;

final class ValueObjectMapTest extends TestCase
{
    private const FIXED_DATA = [
        "id" => "525b4e51-e524-4e5d-8c17-1ef96585cbd3",
        "title" => "hello world!"
    ];

    /**
     * @var ValueObjectMap $valueObjectMap
     */
    private $valueObjectMap;

    public function testCount(): void
    {
        $this->assertCount(10, $this->valueObjectMap);
    }

    protected function setUp(): void
    {
        $this->valueObjectMap = (new ArticleType)
            ->makeEntity(self::FIXED_DATA)
            ->getValueObjectMap();
    }
}
