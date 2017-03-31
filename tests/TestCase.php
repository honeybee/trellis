<?php

namespace Trellis\Tests;

use Trellis\Runtime\EntityTypeInterface;
use Faker;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

abstract class TestCase extends MockeryTestCase
{
    // @codeCoverageIgnoreStart
    protected static $faker;

    public static function setUpBeforeClass()
    {
        self::$faker = Faker\Factory::create();
    }

    protected function getTypeMock($type_name = 'GenericMockType')
    {
        $entity_type_mock = Mockery::mock(EntityTypeInterface::CLASS);
        $entity_type_mock->shouldReceive('getName')->andReturn($type_name);

        return $entity_type_mock;
    }
    // @codeCoverageIgnoreEnd
}
