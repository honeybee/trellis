<?php

namespace Trellis\Tests\Runtime\Attribute\Email;

use Trellis\Runtime\Attribute\Email\EmailAttribute;
use Trellis\Runtime\Attribute\Email\EmailValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use Trellis\Runtime\EntityTypeInterface;
use stdClass;
use Mockery;

/**
 * @todo All commented-out fixtures within the dataProvider methods,
 * must be checked to see if
 * 1. either our expectations are not correct
 * 2. the email validator component we are using needs some help
 *
 * @see http://isemail.info/ for debugging this stuff
 */
class EmailAttributeTest extends TestCase
{
    const ATTR_NAME = 'email';

    public function testCreate()
    {
        $email_attribute = new EmailAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $this->assertEquals($email_attribute->getName(), self::ATTR_NAME);
    }

    public function testCreateValue()
    {
        $email = 'foo.bar@example.com';
        $email_attribute = new EmailAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $value = $email_attribute->createValueHolder();
        $this->assertInstanceOf(EmailValueHolder::CLASS, $value);
        $value->setValue($email);
        $this->assertEquals($email, $value->getValue());
    }

    /**
     * @dataProvider provideValidEmails
     */
    public function testValidEmail($valid_email, $assert_message = '')
    {
        $email_attribute = new EmailAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $result = $email_attribute->getValidator()->validate($valid_email);
        $this->assertEquals(IncidentInterface::SUCCESS, $result->getSeverity(), $assert_message);
    }

    /**
     * @dataProvider provideInvalidEmails
     */
    public function testInvalidEmail($invalid_email, $assert_message = '')
    {
        $email_attribute = new EmailAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $result = $email_attribute->getValidator()->validate($invalid_email);
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $email_attribute = new EmailAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $result = $email_attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::CRITICAL, $result->getSeverity(), $assert_message);
    }

    public function provideValidEmails()
    {
        return [
            [ 'user@example.com' ],
            [ 'user+folder@example.com' ],
            [ 'someone@example.business' ],
            [ 'new-asdf@trololo.co.uk' ],
            [ 'omg@nsfw.xxx' ],
            [
                'A-Za-z0-9.!#$%&*+-/=?^_`{|}~@example.com',
                'A lot of special characters should be valid in the local part of email addresses'
            ],
            [
                "o'hare@example.com",
                'Single quotes are not working'
            ],
            [
                "o'hare@xn--mller-kva.example",
                'International domains should be supported via Punycode ACE strings'
            ],
            [                'user@example123example123example123example123example123example123456.com',
                '63 characters long domain names should be valid'
            ],
            [
                'user@example123example123example123example123example123example123456.co.nz',
                '63 characters long domain names with top level domain "co.nz" should be valid'
            ],
            [
                'example123example123example123example123example123example1234567@example.com',
                '64 characters are valid according to SMTP in the local part'
            ],
            [ 'user@localhost' ],
            // This one should be supported but isn't at the moment
            // [
            //    '"foo bar"@example.com',
            //    'Spaces in email addresses should be allowed when they are in double quotes'
            // ],
        ];
        // @todo add other tests for length constraints
        // - 320 octets overall, 64 for local part according to SMTP, 254 chars overall if you combine RFCs etc.
    }

    public function provideInvalidEmails()
    {
        return [
            // [
            //     'müller@example.com',
            //     'Umlauts in the local part are not allowed'
            // ],
            // [
            //     'umlaut@müller.com',
            //     'Umlauts etc. in the domain part should only be accepted punycode encoded'
            // ],
            [ 'trololo' ],
            [ '@' ],
            [ '<foo>@example.com' ],
            // [ 'a@b' ],
            [
                '<foo>@example.com',
                'Characters < and > should not be not valid in email addresses'
            ],
            [
                'Someone other <someone@example.com>',
                 'Display names with email addresses may be valid, but are not support by us'
            ],
            [
                '"Someone other" <someone@example.com>',
                'Quoted display names with email addresses may be valid, but are not support by us'
            ]
            // [
            //     'user@example123example123example123example123example123example1234567.com',
            //     'Domain names longer than 63 characters are invalid'
            // ],
            // [
            //     'user@' . str_repeat('example123', 20) . '@' . str_repeat('example123', 20) . '.com',
            //     '320 octets/bytes are the maximum allowed length according to RFC 5322 and RFC 5321 valid emails'
            // ],
        ];
    }

    public function provideInvalidValues()
    {
        return [
            [ null ],
            [ false ],
            [ true ],
            [ [] ],
            [ new stdClass() ],
            [ 1 ]
        ];
    }
}
