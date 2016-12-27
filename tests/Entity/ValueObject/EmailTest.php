<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Email;
use Trellis\Entity\ValueObject\Text;
use Trellis\Tests\TestCase;

final class EmailTest extends TestCase
{
    const EMAIL = "peter.parker@example.com";

    /**
     * @var Email $email
     */
    private $email;

    public function testToNative(): void
    {
        $this->assertEquals(self::EMAIL, $this->email->toNative());
        $this->assertEquals(Text::EMPTY, (new Email)->toNative());
    }

    public function testEquals(): void
    {
        $same_email = new Email(self::EMAIL);
        $this->assertTrue($this->email->equals($same_email));
        $different_email = new Email('clark.kent@example.com');
        $this->assertFalse($this->email->equals($different_email));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new Email)->isEmpty());
        $this->assertFalse($this->email->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals(self::EMAIL, (string)$this->email);
    }

    public function testGetLocalPart(): void
    {
        $this->assertEquals("peter.parker", $this->email->getLocalPart());
    }

    public function testGetDomain(): void
    {
        $this->assertEquals("example.com", $this->email->getDomain());
    }

    protected function setUp(): void
    {
        $this->email = new Email(self::EMAIL);
    }
}
