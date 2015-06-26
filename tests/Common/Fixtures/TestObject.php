<?php

namespace Trellis\Tests\Common\Fixtures;

use Trellis\Common\Object;
use Faker\Factory as FakerFactory;

class TestObject extends Object
{
    protected $property_one;

    protected $property_two;

    protected $property_three;

    /**
     * @hiddenProperty
     */
    protected $invisible;

    public function setPropertyOne($property_one)
    {
        $this->property_one = $property_one;
    }

    public function getPropertyOne()
    {
        return $this->property_one;
    }

    public function getPropertyTwo()
    {
        return $this->property_two;
    }

    public function getPropertyThree()
    {
        return $this->property_three;
    }

    public static function createRandomInstance()
    {
        $faker = FakerFactory::create();

        return new static(
            array(
                'property_one' => $faker->word(23),
                'property_two' => $faker->numberBetween(0, 500),
                'property_three' => $faker->boolean()
            )
        );
    }

    public static function createRandomInstances()
    {
        $faker = FakerFactory::create();

        $test_objects = [];
        $max = $faker->numberBetween(1, 15);

        for ($i = 0; $i < $max; $i++) {
            $test_objects[] = self::createRandomInstance();
        }

        return $test_objects;
    }
}
