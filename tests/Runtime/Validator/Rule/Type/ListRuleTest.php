<?php

namespace Trellis\Tests\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Type\ListRule;
use Trellis\Tests\TestCase;
use stdClass;

class ListRuleTest extends TestCase
{
    public function testCreate()
    {
        $rule = new ListRule('list', []);
        $this->assertEquals('list', $rule->getName());
    }

    public function testCastToArraySucceeds()
    {
        $rule = new ListRule('list', [
            ListRule::OPTION_CAST_TO_ARRAY => true
        ]);
        $valid = $rule->apply('foo');
        $this->assertEquals(['foo'], $rule->getSanitizedValue());
    }

    public function testCastToArrayDisabled()
    {
        $rule = new ListRule('list', [
            ListRule::OPTION_CAST_TO_ARRAY => false
        ]);
        $valid = $rule->apply('foo');
        $this->assertFalse($valid);
        $this->assertNull($rule->getSanitizedValue());
    }

    public function testReindexListSucceeds()
    {
        $list = [
            3 => ['a3', 'b3'],
            1 => ['a1', 'b1'],
            2 => ['a2', 'b2'],
        ];
        $rule = new ListRule('list', [
            ListRule::OPTION_REINDEX_LIST => true
        ]);
        $valid = $rule->apply($list);
        $expected = [
            0 => ['a3', 'b3'],
            1 => ['a1', 'b1'],
            2 => ['a2', 'b2'],
        ];
        $this->assertTrue($valid);
        $this->assertEquals($expected, $rule->getSanitizedValue());
    }
}
