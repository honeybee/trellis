<?php

namespace Trellis\Tests\EntityType\Attribute\Uuid;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Uuid\Uuid;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class UuidTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Uuid);
    }

    public function testToNative()
    {
        $value = '375ef3c0-db23-481a-8fdb-533ac47fb9f0';
        $uuid = new Uuid($value);
        $this->assertEquals($value, $uuid->toNative());
        $uuid = new Uuid;
        $this->assertEquals('', $uuid->toNative());
    }

    public function testIsEmpty()
    {
        $uuid = new Uuid('25184b68-6c2d-46b4-8745-46a859f7dd9c');
        $this->assertFalse($uuid->isEmpty());
        $uuid = new Uuid;
        $this->assertTrue($uuid->isEmpty());
        $uuid = new Uuid('');
        $this->assertTrue($uuid->isEmpty());
    }

    public function testGenerate()
    {
        $uuid = Uuid::generate();
        Assertion::uuid($uuid->toNative());

        $this->assertFalse($uuid->isEmpty());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidUuid()
    {
        new Uuid('5184b68-foobar-46b4-8745-46a859f7dd91');
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueFloat()
    {
        new Uuid(42.0);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new Uuid(23);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Uuid(true);
    }
}
