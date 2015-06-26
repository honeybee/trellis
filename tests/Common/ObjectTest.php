<?php

namespace Trellis\Tests\Common;

use Trellis\Tests\Common\Fixtures\TestObject;
use Trellis\Tests\TestCase;

// @todo:
// - test ObjectInterface as nested value (recursively)
// - test empty objects
class ObjectTest extends TestCase
{
    public function testCreate()
    {
        $object_data = $this->getRandomScalarValues();
        $test_object = new TestObject($object_data);

        $this->assertInstanceOf('\\Trellis\\Common\\ObjectInterface', $test_object);
        $this->assertInstanceOf('\\Trellis\\Tests\\Common\\Fixtures\\TestObject', $test_object);
        $this->assertEquals($object_data['property_one'], $test_object->getPropertyOne());
        $this->assertEquals($object_data['property_two'], $test_object->getPropertyTwo());
        $this->assertEquals($object_data['property_three'], $test_object->getPropertyThree());
    }

    public function testToArray()
    {
        $object_data = $this->getRandomScalarValues();
        $object_data['@type'] = 'Trellis\\Tests\\Common\\Fixtures\\TestObject';

        $test_object = new TestObject($object_data);

        $this->assertEquals($object_data, $test_object->toArray());
    }

    protected function getRandomScalarValues()
    {
        return array(
            'property_one' => self::$faker->word(23),
            'property_two' => self::$faker->numberBetween(0, 500),
            'property_three' => self::$faker->boolean()
        );
    }
}
