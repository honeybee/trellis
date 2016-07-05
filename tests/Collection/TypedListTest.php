<?php

namespace Trellis\Tests\Collection;

use Trellis\Collection\CollectionInterface;
use Trellis\Collection\ListInterface;
use Trellis\Collection\TypedList;
use Trellis\Entity\EntityInterface;
use Trellis\Tests\Fixture\Article;
use Trellis\Tests\TestCase;

class TypedListTest extends TestCase
{
    public function testConstruct()
    {
        $list = new TypedList('string', [ 'bar', 'hello world!' ]);

        $this->assertInstanceOf(CollectionInterface::CLASS, $list);
        $this->assertInstanceOf(ListInterface::CLASS, $list);
    }

    /**
     * @expectedException Trellis\Exception
     */
    public function testClassConstraintVioloation()
    {
        $entity = $this->getMockBuilder(EntityInterface::CLASS)->getMock();
        new TypedList(Article::CLASS, [ $entity ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException Trellis\Exception
     */
    public function testIntConstraintVioloation()
    {
        new TypedList('int', [ 42.5 ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException Trellis\Exception
     */
    public function testStringConstraintVioloation()
    {
        new TypedList('string', [ 23 ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException Trellis\Exception
     */
    public function testBoolConstraintVioloation()
    {
        new TypedList('boolean', [ 'foobar' ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException Trellis\Exception
     */
    public function testFloatConstraintVioloation()
    {
        new TypedList('float', [ 5 ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException Trellis\Exception
     */
    public function testArrayConstraintVioloation()
    {
        new TypedList('array', [ 'no-array-here' ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException Trellis\Exception
     */
    public function testScalarConstraintVioloation()
    {
        new TypedList('scalar', [ [ 'no-scalar-here' ] ]);
    } // @codeCoverageIgnore
}
