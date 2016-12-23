<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Email;
use Trellis\Entity\ValueObject\Text;
use Trellis\Tests\TestCase;

final class EmailTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertEquals('peter.parker@example.com', (new Email('peter.parker@example.com'))->toNative());
        $this->assertEquals(Text::EMPTY, (new Email)->toNative());
    }

    public function testEquals(): void
    {
        $email = new Email('peter.parker@example.com');
        $this->assertTrue($email->equals(new Email('peter.parker@example.com')));
        $this->assertFalse($email->equals(new Email('clark.kent@example.com')));
    }
}
