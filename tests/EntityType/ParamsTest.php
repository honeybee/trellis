<?php

namespace Trellis\Tests\Assert;

use Trellis\EntityType\Params;
use Trellis\Tests\TestCase;

final class ParamsTest extends TestCase
{
    public function testGet(): void
    {
        $params = new Params([
           "foo" => [ "bar" => [ [ "snafu" => "fnord" ] ] ]
        ]);
        $this->assertEquals([ "snafu" => "fnord" ], $params->get("foo.bar.0"));
    }

    public function testGetFlat(): void
    {
        $params = new Params([ "foo.bar" => [ "snafu" => "fnord" ] ]);
        $this->assertEquals([ "snafu" => "fnord" ], $params->get("foo.bar", false));
    }
}
