<?php

namespace Trellis\Tests\EntityType\Attribute\Email;

use Trellis\EntityType\Attribute\Email\Email;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class EmailTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Email('test@example.com'));
    }

    public function testToNative()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', $email->toNative());
        $email = new Email;
        $this->assertEquals('', $email->toNative());
    }

    public function testIsEmpty()
    {
        $email = new Email('test@example.com');
        $this->assertFalse($email->isEmpty());
        $email = new Email;
        $this->assertTrue($email->isEmpty());
        $email = new Email('');
        $this->assertTrue($email->isEmpty());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @dataProvider provideInvalidEmails
     */
    public function testInvalidEmailFormat($invalid_email)
    {
        new Email($invalid_email);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueFloat()
    {
        new Email(42.0);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new Email(23);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new Email(true);
    } // @codeCoverageIgnore

    public function provideInvalidEmails()
    {
        return [
            [ 'google.com' ]
        ];
    }
}
