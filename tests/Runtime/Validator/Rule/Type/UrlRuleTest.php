<?php

namespace Trellis\Tests\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Rule\Type\UrlRule;
use Trellis\Tests\TestCase;
use stdClass;

class UrlRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new UrlRule('url', []);
        $this->assertEquals('url', $rule->getName());
    }

    public function testByDefaultInvalidUtf8IsRejected()
    {
        $rule = new UrlRule('url', []);
        $valid = $rule->apply("http://foo\xefbar.de");
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testEmptyStringIsValidByDefault()
    {
        $rule = new UrlRule('url', []);
        $valid = $rule->apply('');
        $this->assertTrue($valid);
        $this->assertEquals('', $rule->getSanitizedValue());
    }

    public function testEmptyStringIsNotValidWhenMandatoryOptionIsSet()
    {
        $rule = new UrlRule('url', ['mandatory' => true]);
        $valid = $rule->apply('');
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testStripInvalidUtf8IfWanted()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_REJECT_INVALID_UTF8 => false ]);
        $valid = $rule->apply("http://foo\xefbar.de/");
        $this->assertTrue($valid);
        $this->assertEquals("http://foobar.de/", $rule->getSanitizedValue());
    }

    public function testByDefaultControlCharactersAreRemovedAndTextIsTrimmed()
    {
        $rule = new UrlRule('url', []);
        $valid = $rule->apply("     http://foo\x00\t\r\nbar.de ");
        $this->assertEquals("http://foobar.de", $rule->getSanitizedValue());
    }

    public function testPunycodeConversion()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_CONVERT_HOST_TO_PUNYCODE => true]);
        $valid = $rule->apply("   http://www.académie-française.fr ");
        $this->assertEquals("http://www.xn--acadmie-franaise-npb1a.fr", $rule->getSanitizedValue());
    }

    public function testLocalhostWithDefaultSchemeForced()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_SCHEME => 'http' ]);
        $valid = $rule->apply("localhost:80/asdf");
        $this->assertEquals("http://localhost:80/asdf", $rule->getSanitizedValue());
    }

    public function testForceHost()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_FORCE_HOST => 'sub.asdf.com' ]);
        $valid = $rule->apply("http://foobar.de:80 ");
        $this->assertEquals("http://sub.asdf.com:80", $rule->getSanitizedValue());
    }

    public function testRequirePort()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_REQUIRE_PORT => true ]);
        $valid = $rule->apply("http://foobar.de ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForcePort()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_FORCE_PORT => 443 ]);
        $valid = $rule->apply("https://foobar.de:80 ");
        $this->assertEquals("https://foobar.de:443", $rule->getSanitizedValue());
    }

    public function testDefaultPort()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_PORT => 443 ]);
        $valid = $rule->apply("https://foobar.de ");
        $this->assertEquals("https://foobar.de:443", $rule->getSanitizedValue());
    }

    public function testRequireUser()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_REQUIRE_USER => true ]);
        $valid = $rule->apply("http://foobar.de ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForceUser()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_FORCE_USER => 'asdf' ]);
        $valid = $rule->apply("https://qwer@foobar.de:80 ");
        $this->assertEquals("https://asdf@foobar.de:80", $rule->getSanitizedValue());
    }

    public function testDefaultUser()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_USER => 'asdf' ]);
        $valid = $rule->apply("https://foobar.de:80 ");
        $this->assertEquals("https://asdf@foobar.de:80", $rule->getSanitizedValue());
    }

    public function testRequirePass()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_REQUIRE_PASS => true ]);
        $valid = $rule->apply("http://asdf@foobar.de ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForceUserPass()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_FORCE_PASS => 'asdf' ]);
        $valid = $rule->apply("https://foo:bar@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80", $rule->getSanitizedValue());
    }

    public function testDefaultUserPass()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_PASS => 'asdf' ]);
        $valid = $rule->apply("https://foo@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80", $rule->getSanitizedValue());
    }

    public function testRequirePath()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_REQUIRE_PATH => true ]);
        $valid = $rule->apply("http://asdf@foobar.de?asdf ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForcePath()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_FORCE_PATH => '/foo/bar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/foo/bar", $rule->getSanitizedValue());
    }

    public function testDefaultPath()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_PATH => '/foo/bar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80?asdf ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/foo/bar?asdf", $rule->getSanitizedValue());
    }

    public function testRequireQuery()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_REQUIRE_QUERY => true ]);
        $valid = $rule->apply("http://asdf@foobar.de/asdf ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForceQuery()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_FORCE_QUERY => 'foo=bar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80/?blah ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/?foo=bar", $rule->getSanitizedValue());
    }

    public function testDefaultQuery()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_QUERY => 'foo=bar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/?foo=bar", $rule->getSanitizedValue());
    }

    public function testRequireFragment()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_REQUIRE_FRAGMENT => true ]);
        $valid = $rule->apply("http://asdf@foobar.de/asdf?asdf ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testForceFragment()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_FORCE_FRAGMENT => 'foobar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80 ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/#foobar", $rule->getSanitizedValue());

        $rule = new UrlRule('url', [ UrlRule::OPTION_FORCE_FRAGMENT => 'foobar' ]);
        $valid = $rule->apply("https://foobar.de/blah/blub#asdf");
        $this->assertEquals("https://foobar.de/blah/blub#foobar", $rule->getSanitizedValue());
    }

    public function testDefaultFragment()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_FRAGMENT => 'foobar' ]);
        $valid = $rule->apply("https://foo:asdf@foobar.de:80/blub?blah ");
        $this->assertEquals("https://foo:asdf@foobar.de:80/blub?blah#foobar", $rule->getSanitizedValue());
    }

    public function testDefaultScheme()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_SCHEME => 'https' ]);
        $valid = $rule->apply("asdf.com:80/blub?blah ");
        $this->assertEquals("https://asdf.com:80/blub?blah", $rule->getSanitizedValue());
    }

    public function testDefaultSchemeIsAddedForValuesThatAreMissingAScheme()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_SCHEME => 'http' ]);
        $valid = $rule->apply("asdf.com:80/blub?blah ");
        $this->assertEquals("http://asdf.com:80/blub?blah", $rule->getSanitizedValue());
    }

    public function testDefaultSchemeIsNotAddedForValuesThatAreMissingAScheme()
    {
        $rule = new UrlRule('url', []);
        $valid = $rule->apply("asdf.com:80/blub?blah ");
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testAllowedSchemesFails()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_ALLOWED_SCHEMES => ['http', 'https'] ]);
        $valid = $rule->apply("ftp://user:pass@asdf.com:21/blub?blah ");
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testAllowedSchemesFtp()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_ALLOWED_SCHEMES => ['ftp'] ]);
        $valid = $rule->apply("ftp://user:pass@asdf.com:21/blub?blah ");
        $this->assertTrue($valid);
        $this->assertEquals("ftp://user:pass@asdf.com:21/blub?blah", $rule->getSanitizedValue());
    }

    public function testDomainSpoofcheckingAutomaticallyConvertsToPunycode()
    {
        // see http://en.wikipedia.org/wiki/IDN_homograph_attack
        $rule = new UrlRule('url', []);
        $cyrillic_domain = "http://wіkіреdіа.org"; // as punycode: xn--http://wkd-8qi2d4hsmbd.org
        $valid = $rule->apply($cyrillic_domain); // contains cyrillic characters instead of simple ascii ones!
        // the domain as punycode is valid, but contains characters from multiple character sets and is thus converted
        $this->assertTrue($valid);
        $this->assertEquals('http://xn--wkd-8cdx9d7hbd.org', $rule->getSanitizedValue());
    }

    public function testSimpleStringRejectionWhenDefaultSchemeIsForced()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_DEFAULT_SCHEME => 'http' ]);
        $js = "localhost.de/some/path";
        $valid = $rule->apply($js);
        $this->assertTrue($valid);
        $this->assertNotNull($rule->getSanitizedValue());
    }

    public function testSimpleUrlRejectionWhenDefaultSchemeIsNotForced()
    {
        $rule = new UrlRule('url', []);
        $js = "localhost.de/some/path";
        $valid = $rule->apply($js);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testSimpleStringRejectionWhenDefaultSchemeIsNotForced()
    {
        $rule = new UrlRule('url', []);
        $js = "localhost";
        $valid = $rule->apply($js);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testSimpleIpRejectionWhenDefaultSchemeIsNotForced()
    {
        $rule = new UrlRule('url', []);
        $js = "194.123.124.35";
        $valid = $rule->apply($js);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testJavascriptSchemeRejection()
    {
        $rule = new UrlRule('url', []);
        $js = "javascript:alert(1)";
        $valid = $rule->apply($js);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testZeroWidthSpaceRejection()
    {
        $rule = new UrlRule('url', []);
        $zero_width_space = "http://domain.com/some\xE2\x80\x8B/path/"; // this will fail FILTER_VALIDATE_URL
        $valid = $rule->apply($zero_width_space);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testRightToLeftOverrideInHostRejection()
    {
        $rule = new UrlRule('url', []);
        $rtlo = "http://\xE2\x80\xAEdomain.com"; // isSuspicious
        $valid = $rule->apply($rtlo);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testLeftToRightOverrideInHostRejection()
    {
        $rule = new UrlRule('url', []);
        $ltro = "http://\xE2\x80\xADdomain.com"; // isSuspicious
        $valid = $rule->apply($ltro);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testRejectSuspiciousHost()
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_ACCEPT_SUSPICIOUS_HOST => false ]);
        $cyrillic_domain = "http://wіkіреdіа.org"; // as punycode: xn--http://wkd-8qi2d4hsmbd.org
        $valid = $rule->apply($cyrillic_domain);
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    /**
     * @dataProvider provideValidUrls
     */
    public function testValidUrl($valid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', []);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid url');
        $this->assertTrue(
            $rule->getSanitizedValue() === $valid_url,
            $assert_message . ' should be set as sanitized url'
        );
    }

    public function provideValidUrls()
    {
        return array(
            array("http://heise.de", 'http://heise.de'),
            array("https://kosme.gr/path?q=1&foo=bar#baz", 'kosme as hostname'),
            array("HTTPS://www.spiegel.de", 'HTTPS://www.spiegel.de'),
            array("http://localhost/test/de/asdf", 'http://localhost/test/de/asdf'),
            array("http://test.sub.domain.domain.com:8080/test", "http://test.sub.domain.domain.com:8080/test"),
            array("http://test-sub-domain.domain.com:8080/test", "http://test-sub-domain.domain.com:8080/test"),
        );
    }

    /**
     * @dataProvider provideValidIdnUrls
     */
    public function testValidIdnUrl($valid_url, $punycode_url, $assert_message = '')
    {
        $rule = new UrlRule('url', [
            UrlRule::OPTION_CONVERT_HOST_TO_PUNYCODE => false,
            UrlRule::OPTION_CONVERT_SUSPICIOUS_HOST => false
        ]);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid url');
        $this->assertEquals(
            $valid_url,
            $rule->getSanitizedValue(),
            $assert_message . ' should be valid and not converted to punycode'
        );
    }

    /**
     * @dataProvider provideValidIdnUrls
     */
    public function testValidPunycodeUrl($valid_url, $punycode_url, $assert_message = '')
    {
        $rule = new UrlRule('url', [ UrlRule::OPTION_CONVERT_HOST_TO_PUNYCODE => true ]);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid url');
        $this->assertEquals($punycode_url, $rule->getSanitizedValue(), $assert_message . ' should be valid punycode');
    }

    public function provideValidIdnUrls()
    {
        return array(
            array(
                "http://wіkіреdіа.org",
                "http://xn--wkd-8cdx9d7hbd.org",
                "wikipedia with chars from belorussia etc."
            ),
            array(
                "https://κόσμε.gr/path?q=1&foo=bar#baz",
                "https://xn--qxajg1a8b.gr/path?q=1&foo=bar#baz",
                'greek word "kosme" as hostname'
            ),
            array(
                "http://스타벅스코리아.com",
                "http://xn--oy2b35ckwhba574atvuzkc.com",
                'http://스타벅스코리아.com'
            ),
            array(
                "http://académie-française.fr",
                "http://xn--acadmie-franaise-npb1a.fr",
                'académie-française.fr'
            ),
            array(
                "http://президент.рф",
                "http://xn--d1abbgf6aiiy.xn--p1ai",
                'президент.рф'
            ),
            array(
                "http://cåsino.com",
                "http://xn--csino-mra.com",
                "http://cåsino.com"
            ),
            array(
                "http://täst.de",
                "http://xn--tst-qla.de",
                "http://täst.de"
            ),
            array(
                "http://müller.de",
                "http://xn--mller-kva.de",
                "http://müller.de"
            ),
        );
    }

    /**
     * @dataProvider provideSuspiciousUrls
     */
    public function testValidSuspicousUrl($valid_url, $valid_punycode_url, $assert_message = '')
    {
        $rule = new UrlRule('url', [ ]);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid url');
        $this->assertEquals(
            $valid_punycode_url,
            $rule->getSanitizedValue(),
            $assert_message . ' as punycode should be set as sanitized url'
        );
    }

    public function provideSuspiciousUrls()
    {
        return array(
            array(
                "http://Рaypal.com",
                "http://xn--aypal-uye.com",
                "suspicious Рaypal.com"
            ),
            array(
                "http://www.payp\xD0\xB0l.com",
                "http://www.xn--paypl-7ve.com",
                "paypal with cyrillic spoof characters"
            ),
        );
    }

    /**
     * @dataProvider provideValidIpv6Urls
     */
    public function testValidIpv6Url($valid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', []);

        $valid = $rule->apply($valid_url);
        $this->assertTrue($valid, $assert_message . ' should be a somewhat valid ipv6 url');
        $this->assertTrue(
            $rule->getSanitizedValue() === $valid_url,
            $assert_message . ' should be set as sanitized url'
        );
    }

    public function provideValidIpv6Urls()
    {
        return array(
            array("http://[2001:0db8:0000:85a3:0000:0000:ac1f:8001]/foo", 'ipv6 w/ path'),
            array("http://[2620:0:2d0:200::10]/foo?bar", "ipv6 w/ brackets, path and query"),
            array("http://[2001:db8:0:85a3:0:0:ac1f:8001]:123/me.html", "ipv6 with brackets should be valid in URLs"),
            array("http://[fe80:0000:0000:0000:0204:61ff:fe9d:f156]/foo", 'normal ipv6 address/foo'),
            array("http://[fe80:0:0:0:204:61ff:fe9d:f156]/foo", 'normal ipv6 address w/o leading zeros /foo'),
            array("http://[fe80::204:61ff:fe9d:f156]/foo", 'normal compressed ipv6 address /foo'),
            array("http://[fe80:0000:0000:0000:0204:61ff:254.157.241.86]/foo", 'ipv6/v4 mix 1'),
            array("http://[fe80:0:0:0:0204:61ff:254.157.241.86]/foo", 'ipv6/v4 mix 2'),
            array("http://[fe80::204:61ff:254.157.241.86]/foo", 'ipv6/v4 mix 3'),
        );
    }

    /**
     * @dataProvider provideIllformedUrls
     */
    public function testIllformedUrl($invalid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', [ 'min' => 8 ]);
        $this->assertFalse($rule->apply($invalid_url), $assert_message . ' should be an invalid url');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should not be set as sanitized url');
    }

    public function provideIllformedUrls()
    {
        return array(
            //array('localhost', 'localhost'),
            array("http://\xfe\x00\x00\n\r\t\n", 'scheme only with invalid utf8 and control chars'),
            array("http://\x00\x00\n\r\t\n\t\n\t\n\t\t\n", 'scheme only with null bytes and control chars'),
            array("les-tilleuls.coop:8080test", 'les-tilleuls.coop:8080test'),
            array(
                "http://test_sub_domain.domain.com:8080/test",
                "Underscore _ is valid in URIs but not in URLs or HOST headers"
            ),
            array("http://testsub+domain.domain.com:8080/test", "Plus sign + is not valid in URLs"),
            array("http://testsub~domain.domain.com:8080/test", "Tilde ~ is valid in URIs but not in URLs"),
            array("http:les-tilleuls.coop", 'http:les-tilleuls.coop'),
            array(
                "http://toolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolongtoolong.com",
                'too long domain'
            ),
            array("[a:b:c:z:e:f:]", '[a:b:c:z:e:f:]'),
            array("http://::1/foo", 'http://::1/foo'),
            array("http://..com", 'no domain label'),
            // the following will hopefully be wrong in more recent versions of PHP's FILTER_VALIDATE_URL
            //array("http://a.-bc.com", 'leading - in domain label'),
            //array("http://ab.cd-.com", 'trailing - in domain label'),
            //array("http://abc.-.abc.com", 'domain label "-"'),
        );
    }

    /**
     * @dataProvider provideInvalidUrls
     */
    public function testInvalidUrl($invalid_url, $assert_message = '')
    {
        $rule = new UrlRule('url', []);
        $this->assertFalse($rule->apply($invalid_url), $assert_message . ' should be an invalid url');
        $this->assertNull($rule->getSanitizedValue(), $assert_message . ' should not be set as sanitized url');
    }

    public function provideInvalidUrls()
    {
        return array(
            array(null, 'NULL'),
            array(false, 'FALSE'),
            array(true, 'TRUE'),
            array(new stdClass(), 'stdClass object'),
        );
    }
}
